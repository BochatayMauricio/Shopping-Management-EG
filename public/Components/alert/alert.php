<?php 
    AlertService::render(); 
?>

<html>
    <script>
        function closeAlert() {
            const alert = document.getElementById('flash-alert');
            if (alert) {
                alert.classList.add('hiding');
                setTimeout(() => alert.remove(), 300);
            }
        }
        
        // Auto-hide después de 5 segundos
        document.addEventListener('DOMContentLoaded', function() {
            const alert = document.getElementById('flash-alert');
            if (alert && alert.dataset.autoHide === 'true') {
                setTimeout(() => {
                    if (alert) {
                        closeAlert();
                    }
                }, 5000);
            }
        });
    </script>

</html>