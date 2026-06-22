<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    /**
     * Helper function to show alerts (useful for AJAX)
     * @param {string} type - 'success' or 'error'
     * @param {string} msg - The message to display
     * @param {string} position - ID of the element to append to, or 'body'
     */
    function alert(type, msg, position='body') {
        let bs_class = (type == 'success') ? 'alert-success' : 'alert-danger';
        let element = document.createElement('div');
        
        // Fixed styling to ensure it is visible over other elements
        element.innerHTML = `
            <div class="alert ${bs_class} alert-dismissible fade show custom-alert" role="alert" style="position: fixed; top: 80px; right: 25px; z-index: 1111;">
                <strong class="me-3">${msg}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        if(position == 'body'){
            document.body.append(element);
        } else {
            document.getElementById(position).appendChild(element);
        }
        
        // Auto-remove the specific element after 2 seconds
        setTimeout(() => {
            element.remove();
        }, 2000);
    }
</script>