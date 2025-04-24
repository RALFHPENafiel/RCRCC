<!-- footer.php -->
</div> <!-- Closing for specific page content -->
    </div> <!-- Closing for page-content-wrapper -->
</div> <!-- Closing for wrapper -->

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menu-toggle');
    const wrapper = document.getElementById('wrapper');
    
    menuToggle.addEventListener('click', function(e) {
        e.preventDefault();
        wrapper.classList.toggle('toggled');
        const icon = this.querySelector('i');
        icon.className = wrapper.classList.contains('toggled') ? 'fas fa-bars' : 'fas fa-times';
    });
    
    function handleResponsive() {
        if (window.innerWidth >= 768) {
            wrapper.classList.remove('toggled');
            menuToggle.querySelector('i').className = 'fas fa-times';
        } else {
            wrapper.classList.add('toggled');
            menuToggle.querySelector('i').className = 'fas fa-bars';
        }
    }
    
    handleResponsive();
    window.addEventListener('resize', handleResponsive);
});
</script>
</body>
</html>