<?php
declare(strict_types=1);

$quizAppScriptUrl = htmlspecialchars(quiz_asset_url('assets/quiz-app.js'), ENT_QUOTES, 'UTF-8');
?>
    <script src="<?= $quizAppScriptUrl ?>" defer></script>
</body>
</html>
