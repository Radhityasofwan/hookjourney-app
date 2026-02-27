<?php
/**
 * CLI: Create User (based on your DB schema)
 *
 * Examples:
 * 1) Auto-create workspace + create leader (workspace owner):
 *    php cli/create_user.php --email="radhit@leader.com" --name="Radhit" --role="leader" --password="Sofwanr27" --workspace="auto" --workspace_name="Hookjourney Workspace"
 *
 * 2) Create team in existing workspace id=5, assign brand_id=12:
 *    php cli/create_user.php --email="team@x.com" --name="Team A" --role="team" --password="123456" --workspace=5 --brand=12 --brand_role="team"
 */

if (php_sapi_name() !== 'cli') {
  http_response_code(403);
  exit("Forbidden\n");
}

$root = realpath(__DIR__ . '/..');

require_once $root . '/config/app.php';
require_once $root . '/config/constants.php';
require_once $root . '/config/database.php';

require_once $root . '/core/Database.php';
require_once $root . '/core/Model.php';

$args = [];
foreach ($argv as $a) {
  if (preg_match('/^--([^=]+)=(.*)$/', $a, $m)) $args[$m[1]] = $m[2];
}

$email         = $args['email'] ?? null;
$name          = $args['name'] ?? null;
$role          = isset($args['role']) ? strtolower(trim($args['role'])) : null;
$password      = $args['password'] ?? null;

$workspaceArg  = $args['workspace'] ?? null;         // number OR "auto"
$workspaceName = $args['workspace_name'] ?? null;    // optional when auto

$brandId       = isset($args['brand']) ? (int)$args['brand'] : null;
$brandRole     = $args['brand_role'] ?? 'team';      // schema brand_members.role_in_brand enum('leader','team','client')

if (!$email || !$name || !$role || !$password) {
  echo "Missing required args.\n";
  echo "Example (auto workspace):\n";
  echo 'php cli/create_user.php --email="leader@demo.com" --name="Leader" --role="leader" --password="Secret!" --workspace="auto" --workspace_name="Hookjourney Workspace"' . "\n";
  exit(1);
}

$allowedRoles = ['leader','team','client'];
if (!in_array($role, $allowedRoles, true)) {
  exit("Invalid role. Use: leader|team|client\n");
}

$db = Database::getInstance()->getConnection();
if (!$db) exit("DB connection failed.\n");

function slugify($text) {
  $text = strtolower(trim($text));
  $text = preg_replace('/[^a-z0-9]+/i', '-', $text);
  $text = trim($text, '-');
  return $text ?: 'workspace';
}

function ensureUniqueWorkspaceSlug(PDO $db, string $baseSlug): string {
  $slug = $baseSlug;
  $i = 0;
  while (true) {
    $st = $db->prepare("SELECT id FROM workspaces WHERE slug = :slug LIMIT 1");
    $st->execute(['slug' => $slug]);
    if (!$st->fetch(PDO::FETCH_ASSOC)) return $slug;
    $i++;
    $slug = $baseSlug . '-' . $i . '-' . substr(bin2hex(random_bytes(3)), 0, 6);
  }
}

function workspaceExists(PDO $db, int $id): bool {
  $st = $db->prepare("SELECT id FROM workspaces WHERE id = :id LIMIT 1");
  $st->execute(['id' => $id]);
  return (bool)$st->fetch(PDO::FETCH_ASSOC);
}

/**
 * Decide workspace id:
 * - If --workspace=auto OR omitted => create new workspace (owner_user_id NULL first)
 * - If numeric => must exist, otherwise error
 */
$workspaceId = null;
$autoWorkspace = false;

if ($workspaceArg === null || strtolower((string)$workspaceArg) === 'auto') {
  $autoWorkspace = true;
} else {
  $workspaceId = (int)$workspaceArg;
  if ($workspaceId <= 0 || !workspaceExists($db, $workspaceId)) {
    exit("ERROR: workspace_id={$workspaceId} not found. Use --workspace=\"auto\" or provide valid id.\n");
  }
}

try {
  // Check existing user by email (schema: users.email unique)
  $st = $db->prepare("SELECT id, email, deleted_at FROM users WHERE email = :email LIMIT 1");
  $st->execute(['email' => $email]);
  $exists = $st->fetch(PDO::FETCH_ASSOC);
  if ($exists && empty($exists['deleted_at'])) {
    exit("User already exists (id={$exists['id']}).\n");
  }

  $db->beginTransaction();

  // 1) Auto-create workspace if requested
  if ($autoWorkspace) {
    $wsName = $workspaceName ?: ("Workspace " . preg_replace('/@.*/', '', $email));
    $baseSlug = slugify($wsName);
    $slug = ensureUniqueWorkspaceSlug($db, $baseSlug);

    $insWs = $db->prepare("INSERT INTO workspaces (name, slug, owner_user_id, plan_type, is_active, created_at)
                           VALUES (:name, :slug, NULL, 'trial', 1, CURRENT_TIMESTAMP)");
    $insWs->execute(['name' => $wsName, 'slug' => $slug]);
    $workspaceId = (int)$db->lastInsertId();
  }

  // 2) Insert user (schema: users.password_hash, users.role_global, users.workspace_id nullable)
  $hash = password_hash($password, PASSWORD_BCRYPT);

  $insUser = $db->prepare("INSERT INTO users (workspace_id, full_name, email, password_hash, role_global, is_active, created_at)
                           VALUES (:workspace_id, :full_name, :email, :password_hash, :role_global, 1, CURRENT_TIMESTAMP)");
  $insUser->execute([
    'workspace_id'   => $workspaceId,  // valid id (or newly created)
    'full_name'      => $name,
    'email'          => $email,
    'password_hash'  => $hash,
    'role_global'    => $role,
  ]);

  $userId = (int)$db->lastInsertId();

  // 3) If role leader + auto workspace: set workspaces.owner_user_id = userId
  if ($role === 'leader' && $workspaceId) {
    $upWs = $db->prepare("UPDATE workspaces SET owner_user_id = :uid WHERE id = :wid");
    $upWs->execute(['uid' => $userId, 'wid' => $workspaceId]);
  }

  // 4) Optional assign brand for team/client (schema brand_members has role_in_brand, can_comment, can_view_finance, is_active)
  if ($role !== 'leader' && $brandId) {
    // idempotent upsert-ish
    $check = $db->prepare("SELECT id FROM brand_members WHERE brand_id = :brand_id AND user_id = :user_id LIMIT 1");
    $check->execute(['brand_id' => $brandId, 'user_id' => $userId]);
    $bm = $check->fetch(PDO::FETCH_ASSOC);

    if ($bm) {
      $up = $db->prepare("UPDATE brand_members
                          SET role_in_brand = :role_in_brand, is_active = 1
                          WHERE id = :id");
      $up->execute(['role_in_brand' => $brandRole, 'id' => $bm['id']]);
    } else {
      $insBm = $db->prepare("INSERT INTO brand_members (brand_id, user_id, role_in_brand, can_comment, can_view_finance, is_active, created_at)
                             VALUES (:brand_id, :user_id, :role_in_brand, 1, 0, 1, CURRENT_TIMESTAMP)");
      $insBm->execute(['brand_id' => $brandId, 'user_id' => $userId, 'role_in_brand' => $brandRole]);
    }
  }

  $db->commit();

  echo "OK created user id={$userId} email={$email} role={$role}\n";
  echo "OK workspace_id={$workspaceId}\n";
  if ($autoWorkspace) echo "OK workspace created automatically\n";
  if ($role !== 'leader' && $brandId) echo "OK assigned brand_id={$brandId} role_in_brand={$brandRole}\n";

} catch (Throwable $e) {
  if ($db->inTransaction()) $db->rollBack();
  echo "ERROR: " . $e->getMessage() . "\n";
  exit(1);
}