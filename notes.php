
<?php
// Folder to store individual notes
$notesFolder = 'notes/';

// Ensure the notes folder exists, create it if not
if (!is_dir($notesFolder)) {
    mkdir($notesFolder, 0777, true);
}

// Add a new note
if (isset($_POST['note']) && !isset($_POST['edit'])) {
    $noteText = $_POST['note'];
    $currentDateTime = date('Y-m-d_H-i-s'); // Format: Year-Month-Day_Hour-Minute-Second
    $noteId = $currentDateTime; // Use date and time as the file name

    // Save the note as a text file with the date and time as the file name
    file_put_contents($notesFolder . $noteId . '.txt', $noteText);
}

// Edit a note
if (isset($_POST['edit'])) {
    $noteId = $_POST['edit'];
    $noteText = $_POST['note'];
    $noteFile = $notesFolder . $noteId . '.txt';
    if (file_exists($noteFile)) {
        file_put_contents($noteFile, $noteText);
    }
}

// Delete a note
if (isset($_GET['delete'])) {
    $noteId = $_GET['delete'];

    // Delete the note file
    $noteFile = $notesFolder . $noteId . '.txt';
    if (file_exists($noteFile)) {
        unlink($noteFile);
    }
}

// Load all notes (get a list of files)
function loadNotes() {
    global $notesFolder;
    $notes = [];
    
    // Get all .txt files in the notes folder
    $files = glob($notesFolder . '*.txt');
    foreach ($files as $file) {
        $noteId = basename($file, '.txt'); // Get the file name without extension
        $noteText = file_get_contents($file);
        $notes[] = ['id' => $noteId, 'text' => $noteText];
    }
    
    // Sort notes by file name (date and time) in descending order (newest first)
    usort($notes, function($a, $b) {
        return strcmp($b['id'], $a['id']); // Compare file names (dates) in reverse order
    });
    
    return $notes;
}

// Get all notes
$notes = loadNotes();
?>