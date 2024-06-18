<?php
session_start();

$words = file('words.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$word = $words[array_rand($words)];

if (!isset($_SESSION['word'])) {
    $_SESSION['word'] = $word;
    $_SESSION['guesses'] = [];
    $_SESSION['attempts'] = 6;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['guess'])) {
        $guess = strtolower($_POST['guess']);
        if (!in_array($guess, $_SESSION['guesses'])) {
            $_SESSION['guesses'][] = $guess;
            if (strpos($_SESSION['word'], $guess) === false) {
                $_SESSION['attempts']--;
            }
        }
    } elseif (isset($_POST['restart'])) {
        session_destroy();
        header("Location: ahorcado.php");
        exit();
    }
}

$wordToDisplay = '';
foreach (str_split($_SESSION['word']) as $char) {
    $wordToDisplay .= in_array($char, $_SESSION['guesses']) ? $char : '_';
}

if ($_SESSION['attempts'] <= 0 || $wordToDisplay === $_SESSION['word']) {
    $gameOver = true;
    $message = $_SESSION['attempts'] <= 0 ? '¡Has perdido! La palabra era: ' . $_SESSION['word'] : '¡Has ganado!';
    session_destroy();
} else {
    $gameOver = false;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ahorcado</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div id="game">
        <h1>Ahorcado</h1>
        <p>Palabra: <?php echo $wordToDisplay; ?></p>
        <p>Intentos restantes: <?php echo $_SESSION['attempts']; ?></p>
        <p>Letras adivinadas: <?php echo implode(', ', $_SESSION['guesses']); ?></p>

        <?php if (!$gameOver): ?>
            <form method="post">
                <input type="text" name="guess" maxlength="1" required>
                <button type="submit">Adivinar</button>
            </form>
        <?php else: ?>
            <p><?php echo $message; ?></p>
            <form method="post">
                <button type="submit" name="restart">Jugar de nuevo</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>