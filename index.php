<?php include 'notes.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Writings</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="shortcut icon" href="https://www.nish.win/facon.png" type="image/x-icon">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
    <form id="note-form">
        <textarea name="note" id="note-text"></textarea><br>
        <button type="submit" class="save-button">Save</button>
        <button type="button" id="toggle-buttons" class="toggle-button">
            <i class="fas fa-toggle-off"></i> <!-- Default to toggle-off -->
        </button>
    </form>

    <div class="notes-container" id="notes-container">
        <?php foreach ($notes as $note): ?>
            <div class="note" data-note-id="<?php echo $note['id']; ?>">
                <button class="edit">
                    <i class="fas fa-pencil"></i> <!-- Edit icon from Font Awesome -->
                </button>
                <button class="delete">
                    <i class="fas fa-xmark"></i> <!-- Delete icon from Font Awesome -->
                </button>
                <!-- Display the raw note text inside a pre element to preserve formatting -->
                <pre><?php echo $note['text']; ?></pre>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        $(document).ready(function() {
            // Initialize toggle state (off by default)
            let toggleState = false;
        
            // Function to control visibility of buttons
            function toggleButtons() {
                if (toggleState) {
                    $('.edit, .delete').show(); // Show edit/delete buttons
                } else {
                    $('.edit, .delete').hide(); // Hide edit/delete buttons
                }
            }
        
            // Toggle button click handler
            $('#toggle-buttons').on('click', function() {
                toggleState = !toggleState; // Toggle the state
                $(this).find('i').toggleClass('fa-toggle-off fa-toggle-on');
                toggleButtons(); // Apply the visibility of buttons based on the toggle state
            });
        
            // Initially set the correct visibility of buttons
            toggleButtons();
        
            // Autofocus the textarea when the page loads
            var newNoteTextarea = $('#note-text').focus()[0];
            adjustTextareaHeight(newNoteTextarea); // Adjust the height of the textarea initially
        
            // Adjust the height of the textarea as the user types
            $('#note-text').on('input', function() {
                adjustTextareaHeight(this);
            });
        
            // Save note using AJAX
            $('#note-form').on('submit', function(event) {
                event.preventDefault(); // Prevent normal form submission
                var noteText = $('#note-text').val(); // Get the note content
                if (noteText.trim() !== "") {
                    $.ajax({
                        type: 'POST',
                        url: 'notes.php',
                        data: { note: noteText },
                        success: function(response) {
                            // Reload the notes container after adding the new note
                            $('#notes-container').load(location.href + ' #notes-container', function() {
                                toggleButtons(); // Ensure the button visibility is correctly applied after reload
                            });
                            $('#note-text').val('');
                            $('#note-text').focus();
                            adjustTextareaHeight(newNoteTextarea); // Reset the height after clearing the textarea
                        }
                    });
                }
            });
        
            // Delete note using AJAX
            $(document).on('click', '.delete', function() {
                var noteId = $(this).closest('.note').data('note-id');
                $.ajax({
                    type: 'GET',
                    url: 'notes.php',
                    data: { delete: noteId },
                    success: function(response) {
                        $('#notes-container').load(location.href + ' #notes-container', function() {
                            toggleButtons(); // Reapply button visibility after deleting
                        });
                    }
                });
            });
        
            // Function to adjust textarea height based on content
            function adjustTextareaHeight(textarea) {
                textarea.style.height = 'auto'; // Reset the height to auto to calculate the new height
                textarea.style.height = textarea.scrollHeight + 'px'; // Set the height to the scroll height
            }
        
            // Edit note functionality
            $(document).on('click', '.edit', function() {
                var note = $(this).closest('.note');
                var noteId = note.data('note-id');
                var noteText = note.find('pre').text();
                
                // Replace the content with a textarea for editing
                note.html(`
                    <textarea class="edit-text">${noteText}</textarea>
                    <button class="save-edit">Save</button>
                    <button class="cancel-edit">Cancel</button>
                `);
            
                // Focus the textarea for editing
                var editTextarea = note.find('.edit-text').focus()[0];
                adjustTextareaHeight(editTextarea); // Adjust the height of the textarea
            });
        
            // Adjust textarea height as the user types (for edit textarea)
            $(document).on('input', '.edit-text', function() {
                adjustTextareaHeight(this);
            });
        
            // Save edited note
            $(document).on('click', '.save-edit', function() {
                var note = $(this).closest('.note');
                var noteId = note.data('note-id');
                var updatedText = note.find('.edit-text').val();
                $.ajax({
                    type: 'POST',
                    url: 'notes.php',
                    data: { edit: noteId, note: updatedText },
                    success: function(response) {
                        $('#notes-container').load(location.href + ' #notes-container', function() {
                            toggleButtons(); // Reapply button visibility after editing
                        });
                    }
                });
            });
        
            // Cancel edit
            $(document).on('click', '.cancel-edit', function() {
                $('#notes-container').load(location.href + ' #notes-container', function() {
                    toggleButtons(); // Reapply button visibility after canceling edit
                });
            });
        });
    </script>
    
</body>
</html>
