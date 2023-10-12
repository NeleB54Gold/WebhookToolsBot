<?php

# Private chat with Bot
if ($v->chat_type == 'private') {
	if ($bot->configs['database']['status'] and $user['status'] !== 'started') $db->setStatus($v->user_id, 'started');
	
	# Start message
	if ($v->command == 'start' or $v->query_data == 'start') {
		$t = $bot->bold('🧰 Webhook Tools') . PHP_EOL . 
		'Welcome to the Bot that will help you manage your Bots Webhooks!' . PHP_EOL . 
		$bot->italic('Send the /help command to know more...', 1);
		if ($v->query_id) {
			$bot->editText($v->chat_id, $v->message_id, $t, $buttons, 'def', 0);
			$bot->answerCBQ($v->query_id);
		} else {
			$bot->sendMessage($v->chat_id, $t, $buttons, 'def', 0);
		}
	}
	# Help message
	elseif ($v->command == 'help' or $v->query_data == 'help') {
		$t = $bot->bold('🆘 How to use me?') . PHP_EOL . 
		'You can send me the API keys, I will give you the Webhook information, you can also deactivate it if you want!';
		if ($v->query_id) {
			$bot->editText($v->chat_id, $v->message_id, $t, $buttons, 'def', 0);
			$bot->answerCBQ($v->query_id);
		} else {
			$bot->sendMessage($v->chat_id, $t, $buttons, 'def', 0);
		}
	}
	# About message
	elseif ($v->command == 'about' or $v->query_data == 'about') {
		$t = $bot->bold('ℹ️ About this Bot') . PHP_EOL . PHP_EOL . 
		'*⃣ PHP: ' . explode('-', phpversion(), 2)[0] . PHP_EOL . 
		'📶 Host: ' . $bot->text_link('Hetzner.cloud', 'https://hetzner.cloud/?ref=tQoUeYbvIstA') . PHP_EOL . 
		'👨🏻‍💻 Developer: @NeleB54Gold' . PHP_EOL . 
		'📢 Updates: @NeleBotsUpdates' . PHP_EOL . 
		'🌐 Other @NeleBots';
		if ($v->query_id) {
			$bot->editText($v->chat_id, $v->message_id, $t, $buttons, 'def', 0);
			$bot->answerCBQ($v->query_id);
		} else {
			$bot->sendMessage($v->chat_id, $t, $buttons, 'def', 0);
		}
	}
	# Bot webhook commands
	else {
		# Unlink webhook
		if (strpos($v->query_data, 'delete ') === 0) {
			$v->query_data = substr($v->query_data, 7);
			$todelete = 1;
		}
		# Get bot webhook info
		if (strpos($v->text, 'bot') === 0) $v->text = substr($v->text, 3);
		if ((!$v->query_data and !$v->command and strpos($v->text, ':') != false and is_numeric(explode(':', $v->text, 2)[0])) or (!$v->command and strpos($v->query_data, ':') != false and is_numeric(explode(':', $v->query_data, 2)[0]))) {
			if ($v->query_data) $v->text = $v->query_data;
			$t = $bot->bold('getMe ');
			$oldtoken = $bot->token;
			$bot->token = $v->text;
			$getMe = $bot->getMe();
			if ($todelete) $bot->deleteWebhook();
			$whinfo = $bot->getWebhookInfo();
			$bot->token = $oldtoken;
			if ($getMe['ok']) {
				$t .= '✅';
				$t .= PHP_EOL . $bot->bold('🤖 Name: ') . $getMe['result']['first_name'];
				$t .= PHP_EOL . $bot->bold('🌐 Username: @') . $getMe['result']['username'];
				$t .= PHP_EOL . $bot->bold('🆔 ID: ') . $getMe['result']['id'];
				$t .= PHP_EOL;
				$t .= PHP_EOL . $bot->bold('getWebhookInfo ');
				if ($whinfo['ok']) {
					$t .= '✅';
					if (empty($whinfo['result']['url'])) {
						$whinfo['result']['url'] = '🔕';
					} else {
						$buttons[][] = $bot->createInlineButton('🙅🏻‍♂️ Unlink webhook', 'delete ' . $v->text);
					}
					$t .= PHP_EOL . '🔗 URL: ' . $whinfo['result']['url'];
					if (isset($whinfo['result']['max_connections'])) $t .= PHP_EOL . '📥 Max connections/s: ' . $whinfo['result']['max_connections'];
					if (isset($whinfo['result']['last_error_message'])) $t .= PHP_EOL . '❌ Last error: ' . $whinfo['result']['last_error_message'];
					if (isset($whinfo['result']['pending_update_count'])) $t .= PHP_EOL . '📬 Pending updates: ' . $whinfo['result']['pending_update_count'];
				} else {
					$t .= '❌';
				}
				$buttons[][] = $bot->createInlineButton('🔄 Update', $v->text);
			} else {
				$t .= '❌';
			}
			if ($v->query_id) {
				$bot->editText($v->chat_id, $v->message_id, $t, $buttons);
				$bot->answerCBQ($v->query_id);
			} else {
				$bot->sendMessage($v->chat_id, $t, $buttons);
			}
			die;
		} else {
			$help = PHP_EOL . 'Try with /help!';
			if ($v->command) {
				$t = '😶 Unknown command...' . $bot->italic($help);
			} elseif ($v->query_data) {
				$t = '😶 Unknown button...' . $help;
			} else {
				$t = '💤 Nothing to do...' . $bot->italic($help);
			}
			if ($v->query_id) {
				$bot->answerCBQ($v->query_id, $t);
			} else {
				$bot->sendMessage($v->chat_id, $t);
			}
		}
	}
}
# Unsupported chats (Auto-leave)
elseif (in_array($v->chat_type, ['group', 'supergroup', 'channels'])) {
	$bot->leave($v->chat_id);
	die;
}

?>
