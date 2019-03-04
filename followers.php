<?php

require_once 'param.php';
require_once 'db.php';
require_once 'common.php';

use \PDO as PDO;

class Followers {
	public static function scrapFollowers(\InstagramAPI\Instagram $ig) {
		$dbConnectionObj = Connection::getInstance();

		// Starting at "null" means starting at the first page.
	    $maxId = null;
	    
	    $followersInsertfields = array('insta_pk', 'insta_username', 'insta_full_name', 'insta_is_verified' );

	    // Generate a random rank token.
	    $rankToken = \InstagramAPI\Signatures::generateUUID();

	    do {
	    	printDebugMessage("Sleeping for few seconds..");
	        sleep(getSleepTime());
	        
	        printDebugMessage("Processing followers..");

	        $followers = $question_marks = $insert_values = array();
	        // Get self followers
	        $response = $ig->people->getSelfFollowers($rankToken,null,$maxId);
	        foreach ($response->getUsers() as $follower) {
	            $followerData = $follower->asStdClass();
	            if(!$followerData->is_verified)
	        		continue;
	            $question_marks[] = '('  . placeholders('?', 4) . ')';
	            $insert_values = array_merge($insert_values, array($followerData->pk, $followerData->username, $followerData->full_name, $followerData->is_verified));
	        }

	        if(empty($question_marks))
	        	continue;

	        $sql = "INSERT INTO followers (" . implode(",", $followersInsertfields ) . ") VALUES " . implode(',', $question_marks);

	        $stmt = $dbConnectionObj->prepare($sql);
	        try {
	            $stmt->execute($insert_values);
	        } catch (\Exception $e) {
	            error_log($e->getMessage());
	            printDebugMessage("Error in inserting followers data : ".$e->getMessage());
	        }

	        $maxId = $response->getNextMaxId();
	    } while ($maxId !== null); // Must use "!==" for comparison instead of "!=".
	}
}