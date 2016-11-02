<?php
	DELIMITER//
	CREATE PROCEDURE getUserRole (OUT roleStatus TINYINT, IN requestId INT)
	  BEGIN
	      SELECT `users`.`role` INTO roleStatus FROM `users`
	        INNER JOIN `campaigns` ON `campaigns`.`user_id` = `users`.`id`
	        INNER JOIN `ad_creative` ON `ad_creative`.`camp_id` = `campaigns`.`id`
	        INNER JOIN `sdk_requests` ON `sdk_requests`.`creative_id` = `ad_creative`.`id`
	      WHERE
	        `sdk_requests`.`id` = requestID;
	  END//
	DELIMITER;

	/** **/
	DELIMITER//
	CREATE PROCEDURE editUserCredit(IN requestId INT)
	BEGIN
		  UPDATE `users`
		    INNER JOIN `applications` ON `applications`.`user_id` = `users`.`id`
	      INNER JOIN `ad_placement` ON `ad_placement`.`app_id` = `applications`.`id`
	      INNER JOIN `sdk_requests` ON `sdk_requests`.`placement_id` = `ad_placement`.`id`
	    SET `users`.`credit` 	= `users`.`credit` + 1,
	    	`users`.`debit`  		= `users`.`debit` + 0.1
	    WHERE
	        `sdk_requests`.`id` = requestId;

	    UPDATE `users`
	      INNER JOIN `campaigns` ON `campaigns`.`user_id` = `users`.`id`
	      INNER JOIN `ad_creative` ON `ad_creative`.`camp_id` = `campaigns`.`id`
	      INNER JOIN `sdk_requests` ON `sdk_requests`.`creative_id` = `ad_creative`.`id`
	    SET `users`.`credit` = `users`.`credit` - 1 
	    WHERE
	          `sdk_requests`.`id` = requestId;
		
	    UPDATE `users`
	    	SET `users`.`credit` = round(`users`.`credit` + 0.1, 1)
	    WHERE `users`.`role` = 2;
	END//
	DELIMITER;

	/** **/
	DELIMITER//
	CREATE PROCEDURE editAdminCredit(IN requestId INT)
		BEGIN
			UPDATE `users` 
				INNER JOIN `applications` ON `applications`.`user_id` = `users`.`id`
        INNER JOIN `ad_placement` ON `ad_placement`.`app_id` = `applications`.`id`
        INNER JOIN `sdk_requests` ON `sdk_requests`.`placement_id` = `ad_placement`.`id`
        SET `users`.`debit` = round(`users`.`debit` - 0.9, 1) 
    	WHERE
        `sdk_requests`.`id` = requestId;

      UPDATE `users`
      	SET `users`.`credit` = round(`users`.`credit` - 1, 1)
      WHERE `users`.`role` = 2;			
		END//
	DELIMITER;

	/** **/
	DELIMITER//
	CREATE PROCEDURE editCredit (IN requestId INT, IN adminPriv TINYINT )
    BEGIN
    	CALL `getUserRole`(requestId, @roleStatus);
    	IF ( SELECT @roleStatus AS userRole ) = adminPriv THEN
    		Call `editAdminCredit`(requestId);
    	ELSE
        	CALL `editUserCredit`(requestId);	
        End IF;
    END//
	DELIMITER;

	/** **/
	DELIMITER //
	CREATE TRIGGER afterAddRequest AFTER INSERT ON `sdk_requests`
		FOR EACH ROW
		BEGIN
		  INSERT INTO `sdk_actions` (`request_id`, `action`, `created_at`, `updated_at`)
		          VALUES( NEW.`id`, 1, NOW(), NOW() );
		END//
	DELIMITER;

	/** **/
	DELIMITER //
	CREATE TRIGGER afterSdkAction AFTER INSERT ON `sdk_actions`
		FOR EACH ROW
		BEGIN
			IF ( NEW.action = 3 )
				CALL `editCredit` (NEW.request_id, 2)
			END IF;
		END//
	DELIMITER;