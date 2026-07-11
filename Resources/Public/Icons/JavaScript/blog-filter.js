document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filter-form') || document.querySelector('form');
    const blogGrid = document.getElementById('blog-grid');

    if (filterForm && blogGrid) {
        
        // --- 1. Form Filter Submit Logic ---
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const searchTitleInput = filterForm.querySelector('[name$="[searchTitle]"]') || filterForm.querySelector('[name="searchTitle"]') || document.getElementById('searchTitle');
            const createDateInput  = filterForm.querySelector('[name$="[createDate]"]')  || filterForm.querySelector('[name="createDate"]')  || document.getElementById('createDate');
            const modifyDateInput  = filterForm.querySelector('[name$="[modifyDate]"]')  || filterForm.querySelector('[name="modifyDate"]')  || document.getElementById('modifyDate');

            const searchTitle = searchTitleInput ? searchTitleInput.value : '';
            const createDate  = createDateInput  ? createDateInput.value  : '';
            const modifyDate  = modifyDateInput  ? modifyDateInput.value  : '';

            console.log("--- Filter Form Submitted ---");
            
            const targetUrl  = filterForm.getAttribute('action') || window.location.href;
            const baseUriPart = targetUrl.split('?')[0];

            blogGrid.style.opacity = '0.5';

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
                    ajax: '1'
                })
            })
            .then(response => {
                if (!response.ok) throw new Error('HTTP status ' + response.status);
                return response.text();
            })
            .then(htmlOutput => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(htmlOutput, 'text/html');
                const targetContainer = doc.querySelector('.frame-type-blogsystem_bloglist');

                if (targetContainer) {
                    blogGrid.innerHTML = targetContainer.innerHTML;
                } else {
                    blogGrid.innerHTML = htmlOutput;
                }

                blogGrid.style.opacity = '1';
            })
            .catch(error => {
                console.error('AJAX Filter Error:', error);
                blogGrid.style.opacity = '1';
            });
        });

        // --- 2. Clear Button Logic ---
        const clearBtn = document.getElementById('clear-filter');
        if (clearBtn) {
            clearBtn.addEventListener('click', function(e) {
                e.preventDefault();

                if (document.getElementById('searchTitle')) document.getElementById('searchTitle').value = '';
                if (document.getElementById('createDate'))  document.getElementById('createDate').value  = '';
                if (document.getElementById('modifyDate'))  document.getElementById('modifyDate').value  = '';

                filterForm.dispatchEvent(new Event('submit'));
            });
        }

        // --- 3. FIXED AJAX PAGINATION LOGIC (Bypassing cHash via POST) ---
        blogGrid.addEventListener('click', function(e) {
            const paginationLink = e.target.closest('.blog-pagination a');
            if (!paginationLink) return;

            e.preventDefault();

            const targetUrl = paginationLink.getAttribute('href');
            if (!targetUrl) return;

            // Extract all parameters from the Fluid link using the URL Object
            const urlObj = new URL(targetUrl, window.location.origin);
            const baseUriPart = targetUrl.split('?')[0];

            // Setup a helper to build form data arguments
            const postData = new URLSearchParams();
            
            // Transfer all URL parameters to the POST body to avoid triggering cHash errors
            urlObj.searchParams.forEach((value, key) => {
                postData.append(key, value);
            });
            
            // Add the AJAX identifier flag for the controller
            postData.append('ajax', '1');

            blogGrid.style.opacity = '0.5';

            // Execute the POST request to bypass cHash validation requirements
            fetch(baseUriPart, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: postData
            })
            .then(response => {
                if (!response.ok) throw new Error('HTTP status ' + response.status);
                return response.text();
            })
            .then(htmlOutput => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(htmlOutput, 'text/html');
                const targetContainer = doc.querySelector('.frame-type-blogsystem_bloglist');

                if (targetContainer) {
                    blogGrid.innerHTML = targetContainer.innerHTML;
                } else {
                    blogGrid.innerHTML = htmlOutput;
                }

                blogGrid.style.opacity = '1';
            })
            .catch(error => {
                console.error('AJAX Pagination Error:', error);
                blogGrid.style.opacity = '1';
            });
        });
    }
});