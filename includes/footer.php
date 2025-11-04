<?php // C:\xampp\htdocs\securigestion\includes\footer.php ?>
    <?php if (is_logged_in()): // Solo muestra el footer si el usuario está logueado ?>
    <footer id="app-footer">
        <p>&copy; <?php echo date('Y'); ?> SecuriGestión Integral</p>
    </footer>
    <?php endif; ?>
    </div>
    
    <script src="js/main.js"></script>
    <script src="js/visitas.js"></script>
    
    </body>
</html>