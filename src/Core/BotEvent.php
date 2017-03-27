<?php

namespace StackGuru\Core;


/**
 * Class BOTEVENT contains different const for keeping a record over what STATE to run commands in
 *
 * SELF = this bot AKA stack-guru
 */
abstract class BotEvent
{
    //    REFERENCE                   	ID                            		// SENDER (IF)  	-> RECEIVER
    const MESSAGE_ALL_I_SELF        	= "message_all";                	// *            	-> *
    const MESSAGE_ALL_E_SELF        	= "message_all_except_self";    	// * != SELF    	-> *
    const MESSAGE_ALL_E_COMMAND        	= "message_all_except_command";    	// * != ! OR @bot   -> *
    const MESSAGE_FROM_SELF         	= "message_self";               	// SELF         	-> *
    const MESSAGE_SELF_TO_SELF      	= "message_self_to_self";       	// SELF         	-> SELF
    const MESSAGE_OTHERS_TO_SELF    	= "message_others_to_self";     	// *            	-> SELF

    const MESSAGE_DELETED				= "message_was_deleted";			// when someone deletes their message.

    const MEMBER_JOINED_GUILD			= "member_joined_guild";			// when someone joins the guild
}
