document.addEventListener("DOMContentLoaded", function() {
    const placeholder = document.getElementById('nav-and-div-placeholder');

    fetch('../navbar_and_Footer/navbar.html')
        .then(response => response.text())
        .then(data => {
            placeholder.innerHTML = data;
        })
        .catch(error => console.error('Error loading navbar and div:', error));

        fetch('../navbar_and_Footer/footer.html')
        .then(response => response.text())
        .then(data => {
            document.getElementById('footer-placeholder').innerHTML = data;
        })
        .catch(error => console.error('Error loading footer:', error));
});
