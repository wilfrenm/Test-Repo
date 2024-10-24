<?php

// Function to parse git diff output
function parseGitDiff($diff_output) {
    $changes = [];
    $currentFile = null;

    foreach ($diff_output as $line) {
        // Detect file change start
        if (preg_match('/^diff --git a\/(.*?) b\/(.*?)/', $line, $matches)) {
            $currentFile = $matches[1];
            $changes[$currentFile] = [
                'status' => 'modified',
                'changes' => []
            ];
        }

        // Detect deleted file
        if (strpos($line, 'deleted file mode') !== false) {
            $changes[$currentFile]['status'] = 'deleted';
        }

        // Detect added or removed lines
        if (preg_match('/^\+(?!\+\+)/', $line)) {
            $changes[$currentFile]['changes'][] = ['type' => 'added', 'line' => $line];
        } elseif (preg_match('/^-(?!\-)/', $line)) {
            $changes[$currentFile]['changes'][] = ['type' => 'removed', 'line' => $line];
        }
    }

    return $changes;
}

// Function to display the parsed summary
function displayDiffSummary($changes) {
    foreach ($changes as $file => $details) {
        echo "File: $file\n";
        
        if ($details['status'] == 'deleted') {
            echo "Status: Deleted\n";
        } else {
            echo "Status: Modified\n";
            if (!empty($details['changes'])) {
                echo "Changes:\n";
                foreach ($details['changes'] as $change) {
                    $action = ($change['type'] == 'added') ? 'Added' : 'Removed';
                    echo "  $action: " . trim($change['line']) . "\n";
                }
            }
        }
        echo "\n";
    }
}

// Function to get git diff output dynamically (via file, stdin, or command)
function getGitDiff($source = 'stdin') {
    $diff_output = [];

    if ($source === 'file') {
        // Read from a file (assumes a file path provided)
        $file_path = 'git_diff_output.txt';  // Change this path if needed
        if (file_exists($file_path)) {
            $diff_output = file($file_path, FILE_IGNORE_NEW_LINES);
        } else {
            echo "Error: File not found.\n";
            exit(1);
        }
    } elseif ($source === 'stdin') {
        // Read from standard input (ideal for shell piping)
        echo "Enter or paste the git diff output (end input with Ctrl+D on UNIX or Ctrl+Z on Windows):\n";
        while ($line = fgets(STDIN)) {
            $diff_output[] = trim($line);
        }
    } elseif ($source === 'command') {
        // Execute the git diff command and capture the output
        $branch1 = 'dev';  // Set your branch names
        $branch2 = 'test';  // Set your branch names
        $command = "git diff $branch1 $branch2";
        exec($command, $diff_output);
    }

    return $diff_output;
}

// Get git diff output dynamically (from stdin, file, or git diff command)
$diff_source = 'command';  // Change to 'file', 'stdin', or 'command' based on your needs
$diff_output = getGitDiff($diff_source);

// Parse and display the git diff summary
if (!empty($diff_output)) {
    $parsed_diff = parseGitDiff($diff_output);
    displayDiffSummary($parsed_diff);
} else {
    echo "No diff data available.\n";
}
die;
?>






















<?php
// Function to get the file differences between two branches
function getCommittedFilesDiff3($branch_1, $branch_3) {
    // Run the git diff command to compare branches and get the list of files
    $command = "git diff $branch_1 $branch_3";
    $output = shell_exec($command);
	// print_r($output);die;
    // Convert the output into an array of file names
    $files = array_filter(explode("\n", $output));
	print_r($files);die;
    return $files;
}

// Example usage
$branch_1 = 'test';
$branch_3 = 'uat';

$committedFilesDiff = getCommittedFilesDiff3($branch_1, $branch_3);

if (!empty($committedFilesDiff)) {
    echo "Files committed to $branch_1 but not in $branch_3:\n";
    foreach ($committedFilesDiff as $file) {
        echo "- $file\n";
    }
} else {
    echo "No differences found between $branch_1 and $branch_3.\n";
}
die;

?>













<?php

	$ch=curl_init();print_r($ch);
	$url="https://api.github.com/repos/wilfrenm/MVC/commits?sha=master";
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	$result=curl_exec($ch);
	echo "<pre>";
	var_dump(json_decode($result,1));die;












// Function to get commits in Development but not in Testing
function getUnmergedCommits($devBranch, $testBranch) {
    // Change to the repository directory
    // chdir('/path/to/your/git/repo');

    // Get the commit IDs that are in Development but not in Testing
    $commits = shell_exec("git log $testBranch..$devBranch --oneline");
	echo "<pre>";
    print_r(array_filter(explode("\n", $commits)));
    return array_filter(explode("\n", $commits));
}

// Function to get files in the latest unmerged commit
function getUnmergedFiles($devBranch, $testBranch) {
    // chdir('/path/to/your/git/repo');

    // Get the file names that are in Development but not in Testing
    $files = shell_exec("git diff --name-only $testBranch..$devBranch");
    print_r(array_filter(explode("\n", $files)));	
    return array_filter(explode("\n", $files));
}

// Example usage
$testBranch = 'branch_1';
$devBranch = 'branch_2';

// Get unmerged commits
$unmergedCommits = getUnmergedCommits($devBranch, $testBranch);
if (!empty($unmergedCommits)) {
    echo "Commits not moved from Development to Testing:\n";
    foreach ($unmergedCommits as $commit) {
        echo "$commit\n";
    }
} else {
    echo "All commits are moved to Testing.\n";
}

// Get files that are not moved from Development to Testing
$unmergedFiles = getUnmergedFiles($devBranch, $testBranch);
if (!empty($unmergedFiles)) {
    echo "Files not moved from Development to Testing:\n";
    foreach ($unmergedFiles as $file) {
        echo "$file\n";
    }
} else {
    echo "All files are moved to Testing.\n";
}
die;
?>











<?php
// Function to get the file differences between two branches
function getCommittedFilesDiff($branch_1, $branch_3) {
    // Run the git diff command to compare branches and get the list of files
    $command = "git diff --name-only $branch_1 $branch_3";
    $output = shell_exec($command);
	// print_r($output);die;
    // Convert the output into an array of file names
    $files = array_filter(explode("\n", $output));
	print_r($files);die;
    return $files;
}

// Example usage
$branch_1 = 'branch_1';
$branch_3 = 'branch_3';

$committedFilesDiff = getCommittedFilesDiff($branch_1, $branch_3);

if (!empty($committedFilesDiff)) {
    echo "Files committed to $branch_1 but not in $branch_3:\n";
    foreach ($committedFilesDiff as $file) {
        echo "- $file\n";
    }
} else {
    echo "No differences found between $branch_1 and $branch_3.\n";
}
die;

?>





















<?php
	
	$var=20;
	class test{
		function test(){
			 // $var=20;
		}
	}
	
	die;
	$arr=array (
 		0 =>
			 array (
				 'id' => 1,
				 'name' => 'John Doe',
				 'age' => 30,
				 'email' => 'john.doe@example.com',
				 'mobile' => 9876543210,
				 'parent_id' => NULL,
			 ),
		 1 =>
			 array (
				 'id' => 2,
				 'name' => 'Jane Smith',
				 'age' => 25,
				 'email' => 'jane.smith@example.com',
				 'mobile' => 9876543211,
				 'parent_id' => NULL,
			 ),
		 2 =>
			 array (
				 'id' => 3,
				 'name' => 'Kumar',
				 'age' => 11,
				 'email' => 'kumar@example.com',
				 'mobile' => 9876543213,
			 ),
		 3 =>
			 array (
				 'id' => 4,
				 'name' => 'Andrew',
				 'age' => 2,
				 'email' => 'andrew@example.com',
				 'mobile' => 9876543210,
				 'parent_id' => 2,
			 ),
		 4 =>
			 array (
				 'id' => 5,
				 'name' => 'David',
				 'age' => 1,
				 'email' => 'david@email.com',
				 'mobile' => 9876543210,
				 'parent_id' => 3,
			 ),
	);
	
	$mobile=[];
	
	foreach($arr as $key=>&$value){
		if($value['age']>12){
			$value['type'] = "adult";
		}
		else if($value['age']>=3 && $value['age']<=12){
			$value['type'] = "child";
		}
		else {
			$value['type'] = "infant";
		}
		if(empty($value['parent_id'])){
			$value['parent_id']="N/A";
		}
		if(!in_array($value['mobile'],$mobile))
			$mobile[$value['id']]=$value['mobile'];
		else{
			foreach($moblie as $key=>$m){
				if($value['mobile']==$m){
					$value['mobile']="Duplicate";
				}
			}
		}
print_r($mobile);
		if(strlen($value['mobile'])==10){
			$value['valid']="true";
		}
		else{
			$value['valid']="false";
		}

	}
	print_r($arr);





?>