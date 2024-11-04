<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'db.php';

$user_id = $_SESSION['user_id'];

// Crear tarea
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task'])) {
    $task = $_POST['task'];
    $status = $_POST['status'];
    $stmt = $pdo->prepare("INSERT INTO tasks (user_id, task, status) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $task, $status]);
    header("Location: tasks.php");
    exit();
}

// Leer tareas
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Eliminar tarea
if (isset($_GET['delete'])) {
    $taskId = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->execute([$taskId, $user_id]);
    header("Location: tasks.php");
    exit();
}

// Actualizar tarea
if (isset($_POST['update_id'])) {
    $updateId = $_POST['update_id'];
    $taskUpdate = $_POST['task_update'];
    $statusUpdate = $_POST['status_update'];
    $stmt = $pdo->prepare("UPDATE tasks SET task = ?, status = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$taskUpdate, $statusUpdate, $updateId, $user_id]);
    header("Location: tasks.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de Tareas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .task-card {
            transition: transform 0.2s;
            margin-bottom: 1rem;
        }

        .task-card:hover {
            transform: translateY(-2px);
        }

        .status-pending {
            background-color: #fff3cd;
        }

        .status-in-progress {
            background-color: #cce5ff;
        }

        .status-completed {
            background-color: #d4edda;
        }

        .status-cancelled {
            background-color: #f8d7da;
        }
    </style>
</head>

<body class="bg-light">
    <nav class="navbar navbar-dark bg-primary mb-4">
        <div class="container">
            <span class="navbar-brand mb-0 h1">Gestor de Tareas</span>
            <a href="logout.php" class="btn btn-outline-light">
                <i class="fas fa-sign-out-alt"></i> Cerrar sesión
            </a>
        </div>
    </nav>

    <div class="container">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title">Nueva Tarea</h5>
                <form action="tasks.php" method="post">
                    <div class="form-row">
                        <div class="col-md-8 mb-3">
                            <label for="task">Descripción de la tarea:</label>
                            <input type="text" name="task" id="task" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="status">Estado inicial:</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="Pendiente">Pendiente</option>
                                <option value="En progreso">En progreso</option>
                                <option value="Completada">Completada</option>
                                <option value="Cancelada">Cancelada</option>
                            </select>
                        </div>
                        <div class="col-md-1 mb-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <?php foreach ($tasks as $task): ?>
                <?php
                $statusClass = '';
                switch ($task['status']) {
                    case 'Pendiente':
                        $statusClass = 'status-pending';
                        break;
                    case 'En progreso':
                        $statusClass = 'status-in-progress';
                        break;
                    case 'Completada':
                        $statusClass = 'status-completed';
                        break;
                    case 'Cancelada':
                        $statusClass = 'status-cancelled';
                        break;
                }
                ?>
                <div class="col-md-6">
                    <div class="card task-card <?= $statusClass ?> shadow-sm">
                        <div class="card-body">
                            <form action="tasks.php" method="post">
                                <div class="form-group">
                                    <input type="text" name="task_update" value="<?= htmlspecialchars($task['task']) ?>"
                                        class="form-control mb-2" required>
                                </div>
                                <div class="form-row align-items-center">
                                    <div class="col">
                                        <select name="status_update" class="form-control">
                                            <option value="Pendiente" <?= $task['status'] == 'Pendiente' ? 'selected' : '' ?>>Pendiente</option>
                                            <option value="En progreso" <?= $task['status'] == 'En progreso' ? 'selected' : '' ?>>En progreso</option>
                                            <option value="Completada" <?= $task['status'] == 'Completada' ? 'selected' : '' ?>>Completada</option>
                                            <option value="Cancelada" <?= $task['status'] == 'Cancelada' ? 'selected' : '' ?>>Cancelada</option>
                                        </select>
                                    </div>
                                    <div class="col-auto">
                                        <input type="hidden" name="update_id" value="<?= $task['id'] ?>">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-save"></i>
                                        </button>
                                        <a href="tasks.php?delete=<?= $task['id'] ?>"
                                            class="btn btn-danger"
                                            onclick="return confirm('¿Estás seguro de que quieres eliminar esta tarea?');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>