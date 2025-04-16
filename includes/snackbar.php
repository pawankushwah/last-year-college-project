<style>
    /* Snackbar base */
    #snackbar {
        visibility: hidden;
        min-width: 250px;
        background-color: #323232;
        color: #fff;
        text-align: center;
        border-radius: 8px;
        padding: 16px;
        position: fixed;
        left: 50%;
        bottom: 30px;
        transform: translateX(-50%);
        z-index: 1000;
        font-size: 16px;

        /* Animation */
        opacity: 0;
        transition: opacity 0.5s ease, bottom 0.5s ease;
    }

    /* Show the snackbar */
    #snackbar.show {
        visibility: visible;
        opacity: 1;
        bottom: 50px;
    }
</style>
<div id="snackbar">This is a message</div>
<script>
  function showSnackbar(message) {
    const snackbar = document.getElementById('snackbar');
    snackbar.textContent = message;
    snackbar.classList.add('show');

    // Hide after 3 seconds
    setTimeout(() => {
      snackbar.classList.remove('show');
    }, 3000);
  }
</script>