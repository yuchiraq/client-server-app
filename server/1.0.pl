#!/usr/bin/perl

use strict;
use IO::Select;
use IO::Socket::INET;

use constant LOGFILE => 'log.txt';
use constant SIZE => 1024;
use constant EOL => "\x0D\x0A";

if(scalar @ARGV < 2)
{
	die "Usage: server.pl ip port\n";
}

my ($serv_ip, $serv_port) = @ARGV;

my $socket = IO::Socket::INET->new(LocalAddr => $serv_ip, LocalPort => $serv_port, Listen => 20, Proto => 'tcp', Reuse => 1) or die $!;
my $select = IO::Select->new($socket) or die $!;

print "Started\n";

while(1)
{
	my @r = $select->can_read;
	my @w = $select->can_write(.1);
	
	for my $handle (@r)
	{
		if($handle eq $socket)
		{
			my $connect = $socket->accept();
			$select->add($connect);
		}
		else
		{
			my $user_input;
			while(sysread $handle, $_, SIZE)
			{
				$user_input .= $_;
				last if $_ =~ /\x0A/ or length $user_input >= SIZE;
			}
			
			$user_input =~ s/[\x00-\x08\x0A-\x1F]//g;
			
			if(length $user_input > 0)
			{
				$user_input = handle_request($user_input, $handle);
				if($user_input)
				{
					syswrite $_, $user_input, SIZE for @w;
				}
			}
			else
			{
				$select->remove($handle);
				close $handle;
			}
		}
	}
}

sub handle_request
{
	my ($user_input, $handle) = @_;
	
	if($user_input eq 'LOG')
	{
		if(-e LOGFILE)
		{
			open F, '<', LOGFILE or warn $!;
			while(<F>)
			{
				s/\x0D\x0A$//g;
				syswrite $handle, $_.EOL, SIZE;
			}
			close F;
		}
		return undef;
	}
	
	$user_input = substr($handle->peerhost.':'.time.':'.$user_input, 0, SIZE - 2).EOL;
	
	open F, '>>', LOGFILE or warn $!;
	syswrite F, $user_input, SIZE;
	close F;
	
	return $user_input;
}