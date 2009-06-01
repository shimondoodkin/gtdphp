<?php

// script that actually performs folder updates


/*
 
Database
    Table for mail settings
        vs config file
    Table for known folders
        Can sync folder list manually via UI button or alternatively in setup screen (rarely needed)
        Folder ID
        Last scanned?
        Folder preferences
            Folder mappings
                Sync - y/n
                Map to specific category and/or context
                Map to specific import type-- action/waiting/reference/project
                    Would suggest NOT doing this automatically with IMAP folder tree to allow flexibility... can store entire folder pathname individually to allow flexibility
    Table for known message IDs
        Message ID
        Item Id
            for association
            Lack of itemID means ignored email
            versus ignored field
        Item Completed?
            needed?
        Need to keep synchronized with current emails
        Folder ID?
            needed? as a hash to speed lookup? (doubt)
            messages can be tracked regardless of place
            if pair messageId with folderId, can use folderId to change item properties (category/context/tag/etc)
    Table for Message ID to Item ID mapping
        Need to synchronize with itemIDs to prevent completed items from showing up as new above
        MessageID
        ItemID
    Need cleanup function
        deleted Items
            itemId from mapping table
                if messageId exists in folder-- keep ID
                alternatively (better?) delete mapping table row
                    then message will be "new" for tracking purposes
        to run manually?
        to run on script execution?
            slow
                will get slower with every email import
            x# of script executions
                counter field in preferences table?

*/