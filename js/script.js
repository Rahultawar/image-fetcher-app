document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('imageSearchForm');
    const inputField = form.querySelector('input[name="txtUserInput"]');
    const resultContainer = document.getElementById('imageResults');
    const showMessage = document.getElementById('showMessage');
    const paginationContainer = document.getElementById('pagination');
    let currentPage = 1;

    // Event listener for form submission
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const userInput = inputField.value.trim().toLowerCase();
        if (userInput) {
            currentPage = 1; // Reset to the first page for a new search
            fetchImages(userInput, currentPage); // Fetch images based on user input
        } else {
            showMessage.textContent = 'Please enter a keyword to search.';
            resultContainer.innerHTML = '';
            paginationContainer.innerHTML = '';
        }
    });

    // Function to fetch images from the backend
    async function fetchImages(userInput, page) {
        const apiUrl = `backend/fetch_image.php`;

        try {
            const response = await fetch(apiUrl, {
                method: 'POST',
                body: new URLSearchParams({ txtUserInput: userInput, page: page }), // Send user input and page number
            });

            if (!response.ok) throw new Error('Error fetching images');

            showMessage.textContent = '';
            const data = await response.text();
            resultContainer.innerHTML = data; // Display the fetched images

            updatePagination(userInput);
        } catch (error) {
            showMessage.textContent = 'Error fetching data from server.';
            console.error(error);
        }
    }

    // Function to update pagination controls
    function updatePagination(userInput) {
        paginationContainer.innerHTML = `
            <button class="btn btn-primary prev" ${currentPage === 1 ? 'disabled' : ''}>Previous</button>
            <span class="mt-2">Page ${currentPage}</span>
            <button class="btn btn-primary next">Next</button>
        `;

        // Add event listener for the "Previous" button
        paginationContainer.querySelector('.prev').addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                fetchImages(userInput, currentPage); // Fetch images for the new page
            }
        });

        // Add event listener for the "Next" button
        paginationContainer.querySelector('.next').addEventListener('click', () => {
            currentPage++;
            fetchImages(userInput, currentPage); // Fetch images for the new page
        });
    }
});
