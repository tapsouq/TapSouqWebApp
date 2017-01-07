<?php
	/* Get User Role Procedure */
	DELIMITER//
	CREATE PROCEDURE `getUserRole`(OUT `roleStatus` TINYINT, IN `requestId` INT)
	BEGIN
		SELECT `users`.`role` INTO roleStatus
	    FROM `users`
			INNER JOIN `campaigns`	  ON `campaigns`.`user_id` = `users`.`id`
			INNER JOIN `ad_creative`  ON `ad_creative`.`camp_id` = `campaigns`.`id`
			INNER JOIN `sdk_requests` ON `sdk_requests`.`creative_id` = `ad_creative`.`id`
		WHERE
		    `sdk_requests`.`id` = requestId;
	END//
	DELIMITER;
	/* End Procedure */

	/** Edit User Credit Procedure**/
	DELIMITER//
	CREATE PROCEDURE `editUserCredit`(IN `requestId` INT)
	BEGIN
		UPDATE `users` `app_users`
			INNER JOIN `applications`   ON `applications`.`user_id` = `app_users`.`id`
		    INNER JOIN `ad_placement` 	ON `ad_placement`.`app_id` = `applications`.`id`
		    INNER JOIN `sdk_requests` 	ON `sdk_requests`.`placement_id` = `ad_placement`.`id`
			INNER JOIN `devices`      	ON `devices`.`id` = `sdk_requests`.`device_id`
		      INNER JOIN `countries`  	ON `countries`.`id` = `devices`.`country`
		SET `app_users`.`credit` = ROUND(`app_users`.`credit` + `countries`.`tier`, 1),
		    `app_users`.`debit`  = ROUND(`app_users`.`debit` + (0.1 * `countries`.`tier`), 1),
	        `app_users`.`updated_at` = NOW()
		WHERE
		    `sdk_requests`.`id` = requestId;
		
		UPDATE `users` `camp_users`
		    INNER JOIN `campaigns` 		ON `campaigns`.`user_id` = `camp_users`.`id`
		    INNER JOIN `ad_creative` 	ON `ad_creative`.`camp_id` = `campaigns`.`id`
		    INNER JOIN `sdk_requests`	ON `sdk_requests`.`creative_id`=`ad_creative`.`id`
			INNER JOIN `devices`      	ON `devices`.`id` = `sdk_requests`.`device_id`
		    INNER JOIN `countries`    	ON `countries`.`id` = `devices`.`country`
		SET `camp_users`.`credit` = ROUND(`camp_users`.`credit` - `countries`.`tier`, 1),
	        `camp_users`.`updated_at` = NOW()
		    WHERE
		          `sdk_requests`.`id` = requestId;
		UPDATE `users` `admin_users`
			INNER JOIN `sdk_requests` ON `sdk_requests`.`id` = requestId
		    INNER JOIN `devices`      ON `devices`.`id` = `sdk_requests`.`device_id`
		    INNER JOIN `countries`    ON `countries`.`id` = `devices`.`country`
		SET `admin_users`.`credit` = ROUND(`admin_users`.`credit` + (0.1 * `countries`.`tier` ), 1),
	        `admin_users`.`updated_at` = NOW()
		WHERE `admin_users`.`role` = 2;
	END//
	DELIMITER;
	/* End Procedure */

	/** Edit Admin Credit Procedure**/
	DELIMITER//
	CREATE PROCEDURE `editAdminCredit`(IN `requestId` INT)
	  BEGIN
	  UPDATE `users` `app_users`
			INNER JOIN `applications` 	ON `applications`.`user_id` = `app_users`.`id`
	    	INNER JOIN `ad_placement`	ON `ad_placement`.`app_id` = `applications`.`id`
	    	INNER JOIN `sdk_requests`	ON `sdk_requests`.`placement_id` = `ad_placement`.`id`
			INNER JOIN `devices`      	ON `devices`.`id` = `sdk_requests`.`device_id`
	    	INNER JOIN `countries`    	ON `countries`.`id` = `devices`.`country`  	
		SET `app_users`.`debit` = ROUND(`app_users`.`debit` - (0.9 * `countries`.`tier` ), 1),
	    	`app_users`.`updated_at` = NOW()
	  	WHERE
	        `sdk_requests`.`id` = requestId;

	  	UPDATE `users` `admin_users`
			INNER JOIN `sdk_requests` ON `sdk_requests`.`id` = requestId
	        INNER JOIN `devices`      ON `devices`.`id` = `sdk_requests`.`device_id`
	        INNER JOIN `countries`    ON `countries`.`id` = `devices`.`country`
	    SET `admin_users`.`credit` = round(`admin_users`.`credit` - (0.9 * `countries`.`tier`), 1),
	    	`admin_users`.`updated_at` = NOW()
	  	WHERE `admin_users`.`role` = 2;			
	END//
	DELIMITER;
	/* End Procedure */

	/** Edit Credit Procedure**/
	DELIMITER//
	CREATE PROCEDURE `editCredit`(IN `requestId` INT)
	BEGIN
		  CALL getUserRole(@roleStatus, requestId);
	    IF ( SELECT @roleStatus AS role ) = 1 THEN
	    	CALL editUserCredit(requestId);
	    ELSE
	    	CALL editAdminCredit(requestId);
	    END IF;
	END//
	DELIMITER;
	/* End Procedure */

	/* Edit Campaign Impressions in today */
	DELIMITER //
	CREATE PROCEDURE `editCampaignImps`(IN `requestId` INT)
	BEGIN
		UPDATE `campaigns` 
			INNER JOIN `ad_creative` 	ON `ad_creative`.`camp_id` = `campaigns`.`id`
			INNER JOIN `sdk_requests` 	ON `sdk_requests`.`creative_id` = `ad_creative`.`id`
		SET 
			`campaigns`.`imp_in_today` = `campaigns`.`imp_in_today` + 1
		WHERE
			`sdk_requests`.`id` = requestId;
	END //
	DELIMITER;
	/* End Procedure */


	/***    Triggers   ***/
	/** After Add Request Trigger**/
	DELIMITER //
	CREATE TRIGGER `afterAddRequest` AFTER INSERT ON `sdk_requests`
	 	FOR EACH ROW
	 	BEGIN
	  		INSERT INTO `sdk_actions` (`request_id`, `action`, `created_at`, `updated_at`)
		  				VALUES( NEW.`id`, 1, NOW(), NOW());
		END
	DELIMITER;
	/* End Trigger */

	/** After Acion Trigger**/
	DELIMITER //
	CREATE TRIGGER `afterAction` AFTER INSERT ON `sdk_actions`
		FOR EACH ROW 
		BEGIN
			/* If action is show, edit campaigns impressions in today */
			IF( NEW.action = 2 ) THEN
				CALL `editCampaignImps` (NEW.request_id);
			/* If action is click, Edit credit for usres. */
			ELSEIF( NEW.action = 3) THEN
		    	CALL `editCredit` (NEW.request_id);
		  	END IF;
		END
	DELIMITER;
	/* End Trigger */