#!/usr/bin/perl

use Encode;
use strict;
use IO::Socket::INET;
use POSIX qw/strftime/;

use Tk;
use Tk::ROText;
use Tk::EntryCheck;

use utf8;
use constant SIZE => 1024;
use constant EOL => "\x0D\x0A";
use open qw(:std :utf8);

BEGIN
{
	if($^O eq 'MSWin32') 
	{
		require Win32::Console;
		Win32::Console::Free();
	}
}

my ($socket, $connection_state) = (0, 0);

my $main = MainWindow->new(title => 'Chat');
$main->geometry('640x420');

my $chat_box = $main->Scrolled
(
	'ROText',
	-scrollbars => 'e'
)->pack(-fill => 'both', -expand => 1, -anchor => 'n');

my $text_field = $main->EntryCheck
(
	-background => 'white',
	-width => 15,
	-maxlength => 100
)->pack(-fill => 'x', -after => $chat_box);
$text_field->bind('<Return>' => \&send);

$main->Label(-text => 'IP')->pack(-side => 'left');

my $ip_field = $main->EntryCheck
(
	-background => 'white',
	-width => 15,
	-maxlength => 15,
	-pattern => qr/[\d\.]/
)->pack(-side => 'left');

$main->Label(-text => 'Порт')->pack(-side => 'left');

my $port_field = $main->EntryCheck
(
	-background => 'white',
	-width => 5,
	-maxlength => 5,
	-pattern => qr/\d/
)->pack(-side => 'left');

my $start_btn = $main->Button
(
	-text => 'Подключиться',
	-command => \&start
)->pack(-side => 'left');

my $clear_btn = $main->Button
(
	-text => 'Очистить чат',
	-command => \&clear_chat
)->pack(-side => 'right');

MainLoop;

sub clear_chat
{
	$chat_box->delete('1.0', 'end');
	$chat_box->insert('end', "Чат очищен\n");
}

sub set_state
{
	$start_btn->configure(-text => $connection_state ? 'Подключиться' : 'Отключиться');
	$connection_state = $connection_state ? 0 : 1;
}

sub disconnect
{
	if($socket)
	{
		syswrite $socket, EOL;
		close $socket;
	}
	set_state();
	clear_chat();
	$chat_box->insert('end', "Вы отключились\n");
}

sub send
{
	unless($connection_state && $socket)
	{
		$main->messageBox
		(
			-message => 'Сначала подключитесь к серверу',
			-title => 'Ошибка',
			-type => 'ok'
		);
		return;
	}
	
	my $text = $text_field->get;
	if(length $text > 0)
	{
		syswrite $socket, $text.EOL, SIZE;
		$text_field->delete('0', 'end');	
	}
}

sub start
{
	if($connection_state)
	{
		disconnect();
	}
	else
	{
		my $serv_ip = $ip_field->get;
		my $serv_port = $port_field->get;
		$serv_ip = decode('utf8',$serv_ip);
		$serv_port = decode('utf8',$serv_port);
		#$serv_ip = encode_utf8($serv_ip);
		#$serv_port = encode_utf8($serv_port);
		if($serv_ip !~ /^(?:\d{1,3}\.){3}\d{1,3}$/ || $serv_port !~ /^\d{1,5}$/)
		{
			$main->messageBox
			(
				-message => 'IP или порт указаны неверно',
				-title => 'Ошибка',
				-type => 'ok'
			);
			return;
		}
	
		$socket = IO::Socket::INET->new(PeerAddr => $serv_ip, PeerPort => $serv_port, PeerProto => 'tcp') or
		{
			$main->messageBox
			(
				-message => 'Не удалось подключиться к серверу',
				-title => 'Ошибка',
				-type => 'ok'
			),
			return
		};
		
		binmode $socket, ':utf8';
		set_state();
		
		if($^O eq 'MSWin32')
		{
			ioctl($socket, 0x8004667e, unpack('I', pack('P', (pack 'L', 1))));
		}
		else
		{
			$socket->blocking(0);
		}

		$socket = decode('utf8',$socket);
		syswrite $socket, 'LOG'.EOL;
	
		while($connection_state)
		{
			if(sysread($socket, $_, SIZE) > 0)
			{
				while($_ =~ /^((?:\d{1,3}\.){3}\d{1,3}):(\d+):(.+)$/mg)
				{
					$chat_box->insert('end', "$1 - ".strftime("%H:%M:%S",localtime($2))."> $3\n");
				}
				$chat_box->see('end');
			}
			$main->update;
			select undef, undef, undef, .1;
		}
	}
}