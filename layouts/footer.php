<script type="text/javascript">
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

</div> <!-- close content div -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<footer>
    <div id="footer" class="container"></div>
</footer>
</body>



</html>


<?php
// clear messages
unset($_SESSION['messages']);
