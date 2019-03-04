<?php

require_once 'param.php';
require_once 'db.php';
require_once 'common.php';

use \PDO as PDO;

class Inbox {
	public static function scrapInbox(\InstagramAPI\Instagram $ig) {
		$dbConnectionObj = Connection::getInstance();

		// Starting at "null" means starting at the first page.
	    $maxId = null;
	    
	    $inboxInsertfields = array('insta_thread_id', 'insta_thread_v2_id', 'insta_pk', 'insta_username', 'insta_full_name', 'insta_is_verified', 'insta_txt' );

	    do {
	    	printDebugMessage("Sleeping for few seconds..");
	        sleep(getSleepTime());
	        printDebugMessage("Processing inbox..");

	        $question_marks = $insert_values = array();

	        // Get inbox
	        $response = $ig->direct->getInbox($maxId);

	       // prea($response->asStdClass()->inbox->threads);
	        $inboxMessages = $response->asStdClass();
	        foreach ($inboxMessages->inbox->threads as $inboxMessage) {
	        	if(empty($inboxMessage->users) || !$inboxMessage->users[0]->is_verified)
	        		continue;
	        	//pre($inboxMessage);
	            $question_marks[] = '('  . placeholders('?', 7) . ')';
	            $insert_values = array_merge($insert_values, array($inboxMessage->thread_id, $inboxMessage->thread_v2_id, $inboxMessage->users[0]->pk, $inboxMessage->users[0]->username, $inboxMessage->users[0]->full_name, $inboxMessage->users[0]->is_verified, $inboxMessage->items[0]->text));
	        }

	        if(empty($question_marks))
	        	continue;

	        $sql = "INSERT INTO inbox (`" . implode("`, `", $inboxInsertfields ) . "`) VALUES " . implode(',', $question_marks);

	        $stmt = $dbConnectionObj->prepare($sql);
	        try {
	            $stmt->execute($insert_values);
	        } catch (\Exception $e) {
	            error_log($e->getMessage());
	            printDebugMessage("Error in inserting inbox data : ".$e->getMessage());
	        }

	        //$maxId = $response->getSeqId();
	        $maxId = $response->getMessage();
	    } while ($maxId !== null); // Must use "!==" for comparison instead of "!=".

	    $maxId = null;
	    do {
	    	printDebugMessage("Sleeping for few seconds..");
	        sleep(getSleepTime());
	        printDebugMessage("Processing pending inbox..");

	        $question_marks = $insert_values = array();

	        // Get inbox
	        $response = $ig->direct->getPendingInbox($maxId);
	        //prea($response,1);
	        //pre(get_class_vars(get_class($response)));
	        //pre($response->asStdClass(),1);
	       // prea($response->asStdClass()->inbox->threads);
	        $inboxMessages = $response->asStdClass();
	        foreach ($inboxMessages->inbox->threads as $inboxMessage) {
	        	if(empty($inboxMessage->users) || !$inboxMessage->users[0]->is_verified)
	        		continue;
	        	//pre($inboxMessage);
	            $question_marks[] = '('  . placeholders('?', 7) . ')';
	            $insert_values = array_merge($insert_values, array($inboxMessage->thread_id, $inboxMessage->thread_v2_id, $inboxMessage->users[0]->pk, $inboxMessage->users[0]->username, $inboxMessage->users[0]->full_name, $inboxMessage->users[0]->is_verified, $inboxMessage->items[0]->text));
	        }

	        if(empty($question_marks))
	        	continue;

	        $sql = "INSERT INTO inbox (" . implode(",", $inboxInsertfields ) . ") VALUES " . implode(',', $question_marks);

	        $stmt = $dbConnectionObj->prepare($sql);
	        try {
	            $stmt->execute($insert_values);
	        } catch (\Exception $e) {
	            error_log($e->getMessage());
	            printDebugMessage("Error in inserting pending inbox data : ".$e->getMessage());
	        }

	        //$maxId = $response->getNextMaxId();
	        $maxId = $response->getMessage();
	    } while ($maxId !== null); // Must use "!==" for comparison instead of "!=".
		
	}
}
