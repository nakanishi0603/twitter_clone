 <?php
  /**
   * htmlspecialcharsの短縮系
   */
  function h($string, $output = true)
  {
    $s = htmlspecialchars($string, ENT_QUOTES);
    if ($output == true) {
      echo $s;
    }
    return $s;
  }

  /**
   * POST判定
   */
  function is_post()
  {
    return $_SERVER["REQUEST_METHOD"] === "POST";
  }
