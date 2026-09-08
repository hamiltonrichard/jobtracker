<?php
/**
 * footer.php
 * Closes the layout containers and the HTML document.
 */
?>
    </div> <!-- End of .main-content (opened in header.php) -->

    <!-- 
      The clear:both and display:block styles prevent the footer from floating 
      into the upper-right whitespace. The margin-left ensures it doesn't 
      overlap with the fixed sidebar [11, 36, Conversation History].
    -->
    <footer style="clear: both; display: block; margin-top: 40px; padding: 20px; text-align: center; font-size: 0.85em; color: #888; border-top: 1px solid #eee; margin-left: 260px;">
        &copy; <?= date('Y') ?> Job Tracker - PHP 8.4 Application
    </footer>
</body>
</html>
