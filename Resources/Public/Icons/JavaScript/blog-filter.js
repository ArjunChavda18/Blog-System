document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filter-form') || document.querySelector('form');
    const blogGrid = document.getElementById('blog-grid');

    if (filterForm && blogGrid) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Select form fields with TYPO3 namespace compatibility
            const searchTitleInput = filterForm.querySelector('[name$="[searchTitle]"]') || filterForm.querySelector('[name="searchTitle"]') || document.getElementById('searchTitle');
            const createDateInput  = filterForm.querySelector('[name$="[createDate]"]')  || filterForm.querySelector('[name="createDate"]')  || document.getElementById('createDate');
            const modifyDateInput  = filterForm.querySelector('[name$="[modifyDate]"]')  || filterForm.querySelector('[name="modifyDate"]')  || document.getElementById('modifyDate');

            const searchTitle = searchTitleInput ? searchTitleInput.value : '';
            const createDate  = createDateInput  ? createDateInput.value  : '';
            const modifyDate  = modifyDateInput  ? modifyDateInput.value  : '';

            console.log("--- Filter Form Submitted ---");
            console.log("Search Title:", searchTitle, "Created:", createDate, "Modified:", modifyDate);

            const targetUrl  = filterForm.getAttribute('action') || window.location.href;
            const baseUriPart = targetUrl.split('?')[0];

            // Show a loading effect while the request is being processed
            blogGrid.style.opacity = '0.5';

            // Send AJAX request to the controller
            fetch(baseUriPart, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    searchTitle: searchTitle,
                    createDate: createDate,
                    modifyDate: modifyDate,
                    ajax: '1' // Flag to identify AJAX request in the controller
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('HTTP status ' + response.status);
                }

                // Read the response as HTML instead of JSON
                return response.text();
            })
            .then(htmlOutput => {
                console.log("--- HTML Response Received ---");

                // Create a temporary DOM parser
                const parser = new DOMParser();

                // Convert the HTML response into a virtual document
                const doc = parser.parseFromString(htmlOutput, 'text/html');

                // Find the blog content container inside the response
                const targetContainer = doc.querySelector('.frame-type-blogsystem_bloglist');

                if (targetContainer) {
                    // Replace the existing blog list with the filtered blog content only
                    blogGrid.innerHTML = targetContainer.innerHTML;
                } else {
                    // Fallback: use the full response if the target container is not found
                    console.warn("Warning: '.container' class not found in AJAX response. Using full output as fallback.");
                    blogGrid.innerHTML = htmlOutput;
                }

                // Restore the normal appearance after loading
                blogGrid.style.opacity = '1';
            })
            .catch(error => {
                console.error('AJAX Filter System Operational Error:', error);
                blogGrid.style.opacity = '1';
            });
        });

        // Handle the Clear button click
        const clearBtn = document.getElementById('clear-filter');
        if (clearBtn) {
            clearBtn.addEventListener('click', function(e) {
                e.preventDefault();

                if (document.getElementById('searchTitle')) document.getElementById('searchTitle').value = '';
                if (document.getElementById('createDate'))  document.getElementById('createDate').value  = '';
                if (document.getElementById('modifyDate'))  document.getElementById('modifyDate').value  = '';

                // Submit the form again after clearing all filters
                filterForm.dispatchEvent(new Event('submit'));
            });
        }
    }
});