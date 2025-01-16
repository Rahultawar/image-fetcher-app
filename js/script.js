document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('imageSearchForm');
    const inputField = form.querySelector('input[name="txtUserInput"]');
    const resultContainer = document.getElementById('imageResults');
    const showMessage = document.getElementById('showMessage');

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        //Get user input
        const userInput = inputField.value.trim().toLowerCase();

        //If userinput is not empty then fetchImages call
        if (userInput) {
            fetchImages(userInput);
        } else {
            showMessage.textContent = 'Please enter a keyword to search.';
            resultContainer.innerHTML = '';
        }
    });

    async function fetchImages(userInput) {
        const apiUrl = `backend/fetch_image.php`;

        try {
            const response = await fetch(apiUrl, {
                method: 'POST',
                body: new URLSearchParams({ txtUserInput: userInput })
            });

            if (!response.ok) {
                throw new Error('Error fetching images');
            }

            showMessage.innerHTML='';
            const data = await response.text();
            resultContainer.innerHTML = data;
        } catch (error) {
            showMessage.textContent = 'Error fetching data from server.';
            console.error(error);
        }
    }
});