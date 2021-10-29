#!/usr/bin/perl

use DBI;
use strict;
use IO::Socket::INET;
use IO::Select;

use constant LOGFILE => 'log.txt';
use constant SIZE => 1024;
use constant EOL => "\x0D\x0A";

my $serv_ip = "127.0.0.1";
my $serv_port = "53";
#my $socket = IO::Socket::INET->new(LocalAddr => $serv_ip, LocalPort => $serv_port, Type => 'tcp', Reuse => 1, Listen => 10) || die "error $!\n";
#my $socket = IO::Socket::INET->new(LocalAddr => $serv_ip, LocalPort => $serv_port, Listen => 10, Proto => 'tcp', Reuse => 1) or die $!;
#my $select = IO::Select->new($socket) or die $!;

my $driver  = "Pg"; 
my $database = "chiraq";
my $dsn = "DBI:$driver:dbname = $database;host = $serv_ip;port = 5432";
my $userid = "chiraq";
my $password = "0812";
my $dbh = DBI->connect($dsn, $userid, $password, { RaiseError => 1 }) 
	or die $DBI::errstr;
print "Opened database successfully\n";


my $stmt = qq(INSERT INTO users_main (user_id, fname, lname, email, phone)
	VALUES (1, 'test', 'test', 'test', 20000 ));
my $rv = $dbh->do($stmt) or die $DBI::errstr;
print "Insert in users_main is done\n";


print "---users_main---\n\n";
my $stmt = qq(SELECT * FROM users_main;);
my $sth = $dbh->prepare( $stmt );
my $rv = $sth->execute() or die $DBI::errstr;
if($rv < 0) {
	print $DBI::errstr;
}

while(my @row = $sth->fetchrow_array()) {
    print "ID = ". $row[0] . "\n";
    print "ID_BOOK = ". $row[1] . "\n";
    print "NAME = ". $row[2] ."\n";
    print "LAST NAME = ". $row[3] ."\n";
    print "EMAIL =  ". $row[4] ."\n";
    print "PHONE NUMBER =  ". $row[5] ."\n\n";
}

print "---users_university---\n\n";
my $stmt = qq(SELECT * FROM users_university;);
my $sth = $dbh->prepare( $stmt );
my $rv = $sth->execute() or die $DBI::errstr;
if($rv < 0) {
   print $DBI::errstr;
}

while(my @row = $sth->fetchrow_array()) {
    print "ID = ". $row[0] . "\n";
    print "ID_BOOK = ". $row[1] . "\n";
    print "Group NAME = ". $row[2] ."\n";
    print "User Rank = ". $row[3] ."\n";
}

$dbh->disconnect();
