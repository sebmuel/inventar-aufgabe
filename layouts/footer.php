</div> <!-- close content div -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<footer>
    <div id="footer" class="container"></div>
</footer>

<script type="text/javascript">
    // sidebar dropdown handle
    const toggleButtons = document.querySelectorAll("div.has-dropdown");

    const toggleDropdown = (event) => {
        const dropdown = event.target.children[1];
        if (dropdown != undefined) {
            dropdown.classList.toggle("open")
        }
    }

    toggleButtons.forEach(button => {
        button.addEventListener('click', toggleDropdown);
    })
</script>

<script type="text/javascript">
    const messageElement = document.getElementById("message");
    if (messageElement) {
        messageElement.classList.add('toggle');
    }
</script>

</body>



</html>


<?php
// clear messages
unset($_SESSION['message']);
