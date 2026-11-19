// holds the marathon data fetched from the JSON file
let marathonData = [];
let currentMarathonIndex = 0; // Variable to keep track of the current marathon index
let originalMarathonData = []; // Variable to hold the original marathon data
// Track whether Edit Mode is enabled
let editMode = false;

// Toggle Edit Mode on and off
function toggleEditMode() {

    // Switch the edit mode state
    editMode = !editMode;

    // List of editing fields
    let editFields = [
        'editRaceName',
        'editLocation',
        'editYear',
        'editCompleted',
        'editRaceType',
        'editImage'
    ];

    // Enable or disable each editing field
    editFields.forEach(function(field) {
        document.getElementById(field).disabled = !editMode;
    });

    // Update the button text
    document.getElementById('editToggle').textContent =
        editMode ? 'Disable Edit Mode' : 'Enable Edit Mode';

    // Enable or disable the Save Changes button
    document.getElementById('saveButton').disabled = !editMode;

    // Enable or disable the Delete button
    document.getElementById('deleteButton').disabled = !editMode;
}
// Creating an XMLHttpRequest object
let xhr = new XMLHttpRequest();

// Open a GET request to the getMarathons.php file
xhr.open('GET', 'fetch_data.php', true);

// Setting the response type to JSON
xhr.responseType = 'json';

// Convert JSON text into a JavaScript object
xhr.onload = function() {
    if (xhr.status === 200) {
        let data = xhr.response;

    marathonData = data;
    originalMarathonData = [...data];

    showMarathonData();

    // Check whether any marathon records exist
    if (marathonData.length > 0) {
        getItem(0);
    } else {
        document.getElementById('raceName').textContent =
            'No marathon records found.';
        document.getElementById('location').textContent = '';
        document.getElementById('year').textContent = '';
        document.getElementById('completed').textContent = '';
        document.getElementById('raceType').textContent = '';
        document.getElementById('raceImage').src = '';
        document.getElementById('itemCounter').textContent = '0 of 0';
    }
    } else {
    console.error('Error fetching marathon data:', xhr.statusText);

    document.getElementById('raceName').textContent =
        'Unable to load marathon records. Please try again.';
    }
};
// Handle network errors during initial data loading
xhr.onerror = function() {
    document.getElementById('raceName').textContent =
        'Network error: Unable to connect to the server.';
};
// Send the request
xhr.send();

// Function to display the marathon data in the HTML
function showMarathonData() {
    let marathonList = document.getElementById('marathon-list');

    // Clear the current list before displaying it again
    marathonList.innerHTML = '';

    marathonData.forEach(function(marathon) {
        let listItem = document.createElement('li');

        listItem.textContent =
            marathon.raceName + ' - ' +
            marathon.location + ' - ' +
            marathon.year;

        marathonList.appendChild(listItem);
    });
}
function getItem(i) {
    // Check that the index is valid
    if (i < 0 || i >= marathonData.length) {
        return;
    }

    // Display marathon data at index i
    let marathon = marathonData[i];

    document.getElementById('raceName').textContent = marathon.raceName;
    document.getElementById('location').textContent = marathon.location;
    document.getElementById('year').textContent = marathon.year;
    document.getElementById('completed').textContent =
        marathon.completed ? 'Completed' : 'Not Completed';
    document.getElementById('raceType').textContent = marathon.raceType;
    document.getElementById('raceImage').src = marathon.image;
    document.getElementById('editRaceName').value = marathon.raceName;
    document.getElementById('editLocation').value = marathon.location;
    document.getElementById('editYear').value = marathon.year;
    document.getElementById('editCompleted').value = marathon.completed.toString();
    document.getElementById('editRaceType').value = marathon.raceType;

    // Update the current index
    currentMarathonIndex = i;

    // Display the current item number and total
    document.getElementById('itemCounter').textContent =
        (i + 1) + ' of ' + marathonData.length;
}

//functino nextItem()
function nextItem() {
    if (currentMarathonIndex < marathonData.length - 1) {
        getItem(currentMarathonIndex + 1);
    }
}

//function lastItem()
function lastItem() {
    getItem(marathonData.length - 1);
}
//function previousItem()
function previousItem() {
    if (currentMarathonIndex > 0) {
        getItem(currentMarathonIndex - 1);
    }
}
//function firstItem()
function firstItem() {
    getItem(0);
}

//function searchingMarathon() by race index
function searchingMarathon() {
    let index = document.getElementById('searchIndex').value;
    // convert index to integer
    index = Number(index) - 1; // convert to 0-based index
    //check if index is valid
    if (Number.isInteger(index) && index >= 0 && index < marathonData.length) {
        getItem(index); // convert to 0-based index
    } else {
        alert('Invalid index. Please enter a number between 1 and ' + marathonData.length);
    }
}
//function sortMarathons() by race name
function sortMarathons(order) {
    // Get the selected property from the dropdown
    let property = document.getElementById('sortBy').value;

    // Sort the marathon array
    marathonData.sort(function(a, b) {
        let valueA = a[property];
        let valueB = b[property];

        // Convert strings to lowercase for comparison
        if (typeof valueA === 'string') {
            valueA = valueA.toLowerCase();
            valueB = valueB.toLowerCase();
        }

        // Compare the two values
        if (valueA < valueB) {
            return order === 'asc' ? -1 : 1;
        }

        if (valueA > valueB) {
            return order === 'asc' ? 1 : -1;
        }

        return 0;
    });

    // Refresh the displayed list
    showMarathonData();

    // Display the first marathon after sorting
    getItem(0);
}
//function OriginalOrder()
function OriginalOrder() {
    marathonData = [...originalMarathonData]; // Restore the original data
    showMarathonData();
    getItem(0); // Display the first item after restoring the original order
}

//allow edits to be saved in with saveMarathon()

function saveMarathon() {
    // Prevent saving when Edit Mode is disabled
    if (!editMode) {
        return;
    }
    // Get values from the editing fields
    let raceName = document.getElementById('editRaceName').value.trim();
    let location = document.getElementById('editLocation').value.trim();
    let year = Number(document.getElementById('editYear').value);
    let raceType = document.getElementById('editRaceType').value.trim();

    // Check for empty fields
    if (raceName === '' || location === '' || raceType === '') {
        document.getElementById('editMessage').textContent =
            'Please fill in all required fields.';
        return;
    }

    // Check that the year is valid
    if (!Number.isInteger(year) || year < 2000 || year > 2100) {
        document.getElementById('editMessage').textContent =
            'Please enter a valid year between 2000 and 2100.';
        return;
    }

    // Get the currently selected marathon
    let marathon = marathonData[currentMarathonIndex];
    // Get the newly selected image, if one was chosen
    let image = document.getElementById('editImage').files[0];
    
    // Test Cases for an earlier error occuring
    //console.log("SAVE MARATHON FUNCTION RUNNING");
    //console.log("Selected marathon:", marathon);
    //console.log("Marathon ID:", marathon.id);

    // Create a FormData object
    let formData = new FormData();

    // Add the updated marathon information
    formData.append('id', marathon.id);
    formData.append('raceName', raceName);
    formData.append('location', location);
    formData.append('year', year);
    formData.append(
        'completed',
        document.getElementById('editCompleted').value
    );
    formData.append('raceType', raceType);

    // Add the image only if a new one was selected
    if (image) {
        formData.append('image', image);
    }

    // Create an XMLHttpRequest
    let xhrUpdate = new XMLHttpRequest();

    // Send the updated marathon to PHP
    xhrUpdate.open('POST', 'updateMarathon.php', true);

    xhrUpdate.onload = function() {
    let response;

    try {
        response = JSON.parse(xhrUpdate.responseText);
    } catch (error) {
        document.getElementById('editMessage').textContent =
            'Error: Invalid response from server.';
        return;
    }

        if (xhrUpdate.status === 200 && response.success) {

            // Update the marathon information in the JavaScript array
            marathon.raceName = raceName;
            marathon.location = location;
            marathon.year = year;
            marathon.completed =
                document.getElementById('editCompleted').value === 'true';
            marathon.raceType = raceType;
            marathon.image = response.image;

            // Refresh the displayed information
            showMarathonData();
            getItem(currentMarathonIndex);

            document.getElementById('editMessage').textContent =
                'Marathon saved successfully!';

        } else {
            document.getElementById('editMessage').textContent =
                'Error: ' + (response.message || 'Unable to save marathon.');
        }
    };
    // Handle network errors
        xhrUpdate.onerror = function() {
            document.getElementById('editMessage').textContent =
                'Network error: Unable to connect to the server.';
        };
    // Send the updated marathon information and image to PHP
    xhrUpdate.send(formData);
}

function insertMarathon() {
    // Validate the inputs
    let raceName = document.getElementById('newRaceName').value.trim();
    let location = document.getElementById('newLocation').value.trim();
    let year = Number(document.getElementById('newYear').value);
    let completed = document.getElementById('newCompleted').value;
    let raceType = document.getElementById('newRaceType').value.trim();

    // Get the selected image
    let image = document.getElementById('newImage').files[0];

    // Check for empty fields
    if (raceName === '' || location === '' || raceType === '') {
        document.getElementById('insertMessage').textContent =
            'Please fill in all required fields.';
        return;
    }

    // Check that the year is valid
    if (!Number.isInteger(year) || year < 2000 || year > 2100) {
        document.getElementById('insertMessage').textContent =
            'Please enter a valid year between 2000 and 2100.';
        return;
    }

    // Create a FormData object
    let formData = new FormData();

    // Add the marathon information
    formData.append('raceName', raceName);
    formData.append('location', location);
    formData.append('year', year);
    formData.append('completed', completed);
    formData.append('raceType', raceType);

    // Add the image if one was selected
    if (image) {
        formData.append('image', image);
    }

    // Create an XMLHttpRequest
    let xhrInsert = new XMLHttpRequest();

    // Send the new marathon to PHP
    xhrInsert.open('POST', 'insertMarathon.php', true);

    // Handle the PHP response
    xhrInsert.onload = function() {
    let response;

    try {
        response = JSON.parse(xhrInsert.responseText);
    } catch (error) {
        document.getElementById('insertMessage').textContent =
            'Error: Invalid response from server.';
        return;
    }

    if (xhrInsert.status === 200) {

            if (response.success) {
                // Add the new marathon to the JavaScript arrays
                marathonData.push(response.marathon);
                originalMarathonData.push(response.marathon);

                // Refresh the list
                showMarathonData();

                // Display the newly inserted marathon
                getItem(marathonData.length - 1);

                document.getElementById('insertMessage').textContent =
                    'Marathon added successfully!';
                // Clear the insertion form after a successful addition
                document.getElementById('newRaceName').value = '';
                document.getElementById('newLocation').value = '';
                document.getElementById('newYear').value = '';
                document.getElementById('newCompleted').value = 'false';
                document.getElementById('newRaceType').value = '';
                document.getElementById('newImage').value = '';
            } else {
                document.getElementById('insertMessage').textContent =
                    'Error: ' + response.message;
            }
        } else {
            document.getElementById('insertMessage').textContent =
                'Error inserting marathon.';
        }
    };
    // Handle network errors
    xhrInsert.onerror = function() {
        document.getElementById('insertMessage').textContent =
            'Network error: Unable to connect to the server.';
    };

    // Send the FormData to PHP
    xhrInsert.send(formData);
}

function deleteMarathon() {
    // Prevent deleting when Edit Mode is disabled
    if (!editMode) {
        return;
    }
    // Check whether any marathons exist
    if (marathonData.length === 0) {
        return;
    }
    // Get the currently selected marathon
    let marathon = marathonData[currentMarathonIndex];

    // Ask for confirmation before deleting
    let confirmDelete = confirm(
        'Are you sure you want to delete ' + marathon.raceName + '?'
    );

    if (!confirmDelete) {
        return;
    }

    // Create an XMLHttpRequest
    let xhrDelete = new XMLHttpRequest();

    // Send the selected marathon ID to PHP
    xhrDelete.open('POST', 'delMarathon.php', true);
    xhrDelete.setRequestHeader('Content-Type', 'application/json');

    xhrDelete.onload = function() {
    let response;

    try {
        response = JSON.parse(xhrDelete.responseText);
    } catch (error) {
        document.getElementById('editMessage').textContent =
            'Error: Invalid response from server.';
        return;
    }

    if (xhrDelete.status === 200) {

            if (response.success) {
                // Remove the marathon from the JavaScript arrays
                marathonData.splice(currentMarathonIndex, 1);

                originalMarathonData = originalMarathonData.filter(
                    item => item.id !== marathon.id
                );

                // Display the updated list
                showMarathonData();

                // Display the first remaining marathon
                if (marathonData.length > 0) {
                    getItem(0);
                } else {
                    document.getElementById('raceName').textContent = '';
                    document.getElementById('location').textContent = '';
                    document.getElementById('year').textContent = '';
                    document.getElementById('completed').textContent = '';
                    document.getElementById('raceType').textContent = '';
                    document.getElementById('raceImage').src = '';
                    document.getElementById('itemCounter').textContent = '0 of 0';
                }

                document.getElementById('editMessage').textContent =
                    'Marathon deleted successfully!';
            } else {
                document.getElementById('editMessage').textContent =
                    'Error: ' + response.message;
            }
        } else {
            document.getElementById('editMessage').textContent =
                'Error deleting marathon.';
        }
    };
    // Handle network errors
    xhrDelete.onerror = function() {
        document.getElementById('editMessage').textContent =
            'Network error: Unable to connect to the server.';
    };

    // Send the ID as JSON
    xhrDelete.send(JSON.stringify({ id: marathon.id }));
}
