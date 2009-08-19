<?php

include "unittest.php";
include "addict_database.php";
include "scrape.php";

class AddictTests extends UnitTest {

    function __construct($debug){
        $this->r = new AddictDatabase("brokenva_addicttest");
        parent::__construct($debug);
    }

	function testCreateObject(){
		self::assert_notnull($this->r);
	}
	
	/** User Tests **/
	function testAddUser(){
		$add = $this->r->addUser(2);
		self::assert_true($add);
	}
	
	function testAddMovie(){
		$add = $this->r->addMovie(2, "Title", 100, 99);
		$add = $this->r->addMovie(3, "Title Goes Here", NULL, 99);
		$add = $this->r->addMovie(4, "Title", 100);
		self::assert_true($add);
	}


	function testGetUser(){
		$user = $this->r->getUser(2);
		self::assert_equals($user['id'], 2);
		self::assert_equals($user['count'], 0);
		$user = $this->r->getUser(14);
		self::assert_false($user);
	}
	
	function testGetUsers(){
		$users = $this->r->getUsers();
		self::assert_notnull($users['2']);
	}
	
	function testGetUsersIds(){
		$users = $this->r->getUsers();
		self::assert_true(in_array(2, array_keys($users)));
	}

	function testGetMovies(){
		$add = $this->r->getMovies();
		self::assert_equals($add['2']['id'], 2);
		self::assert_equals($add['2']['title'], "Title");
		self::assert_equals($add['2']['imdb_rank'], 100);
		self::assert_equals($add['2']['afi_rank'], 99);	
	}
	
	/** Movie Tests **/
	function testGetMovie(){
		$add = $this->r->getMovie(2);	
		self::assert_equals($add['id'], 2);
		self::assert_equals($add['title'], "Title");
		self::assert_equals($add['imdb_rank'], 100);
		self::assert_equals($add['afi_rank'], 99);
		self::assert_equals($add['instant'], 0);
		$add = $this->r->getMovie(1);
		self::assert_false($add);	
					
	}
	
	function testGetRankedMovies(){
		$this->rank = $this->r->getRankedMovies();	
		self::assert_equals($this->rank['2']['id'], 2);
		self::assert_equals($this->rank['2']['title'], "Title");
		self::assert_equals($this->rank['2']['imdb_rank'], 100);
		self::assert_equals($this->rank['2']['afi_rank'], 99);				
	}
	
	function testGetIMDBMovies(){
		$add = $this->r->getIMDBMovies();	
		self::assert_equals($add['2']['id'], 2);
		self::assert_equals($add['2']['title'], "Title");
		self::assert_equals($add['2']['imdb_rank'], 100);
		self::assert_equals($add['2']['afi_rank'], 99);				
	}


	function testGetAFIMovies(){
		$add = $this->r->getAFIMovies();	
		self::assert_equals($add['2']['id'], 2);
		self::assert_equals($add['2']['title'], "Title");
		self::assert_equals($add['2']['imdb_rank'], 100);
		self::assert_equals($add['2']['afi_rank'], 99);				
	}
	
	function testUpdateTitle(){
		$add = $this->r->updateMovieTitle(2, "New Title");
		self::assert_true($add);
	}
	
    function testUpdateNetflixInstant(){
		$add = $this->r->updateNetflixInstant(2, true);
		self::assert_true($add);
		$add = $this->r->getMovie(2);
		self::assert_true($add);
		self::assert_equals($add['instant'], 1);
		
	}

    function testUpdateNetflixId(){
		$add = $this->r->updateNetflixId(2, 8);
		$add = $this->r->getMovie(2);
		self::assert_true($add);
		self::assert_equals($add['netflix_id'], 8);	
	}
	
	function testUpdateRank(){
		$this->r->updateIMDBRank(2, 4);
		$this->r->updateAFIRank(2, 4);
		$add = $this->r->getMovie(2);	
		self::assert_equals($add['id'], 2);
		self::assert_equals($add['title'], "New Title");
		self::assert_equals($add['imdb_rank'], 4);
		self::assert_equals($add['afi_rank'], 4);	
	}

	/** User Movie Tests **/
	function testaddUserMovie(){
		$add = $this->r->addUserMovie(2,2);
		self::assert_true($add);
		$add = $this->r->addUserMovie(2,2);
		self::assert_true($add);
	}

	function testgetUserMovies(){
		$add = $this->r->getUserMovies(2);
		self::assert_equals(count($add), 1);
		self::assert_true(in_array(2, $add));
	}
	
	function testgetMovieUsers(){
		$add = $this->r->getUserMovies(2);
		self::assert_equals(count($add), 1);
		self::assert_true(in_array(2, $add));
	}
	
	function testupdateUserCount(){
		$update = $this->r->updateUserMovieCount(2);
		$user = $this->r->getUser(2);
		self::assert_equals($user['count'], 1);
		self::assert_equals($user['percent'], .36);
		self::assert_true($update);
	}
	
	function testRemoveUserMovie(){
		$delete = $this->r->deleteUserMovie(2,2);
		self::assert_true($delete);
	}
	
	function testRemoveUser(){
		$delete = $this->r->deleteUser(2);
		self::assert_true($delete);
	}
	
	function testRemoveMovie(){
		$delete = $this->r->deleteMovie(2);
		$delete = $this->r->deleteMovie(3);
		$delete = $this->r->deleteMovie(4);
		self::assert_true($delete);
	}
	
	/** Scraping Test **/
	function testIMDBScrape(){
		$top250 = getIMDB250();
		//print_r($top250);
		self::assert_equals(count($top250), 250);
		self::assert_equals($top250['111161']['title'], "The Shawshank Redemption");
	}
	
/*
	function testAFIScrape(){
		$afi100 = getMoviesFile("lists/afi_names.txt");
		print_r($afi100);
		self::assert_equals(count($afi100), 100);
		self::assert_equals($afi100['33467']['title'], "Citizen Kane");
		self::assert_equals($afi100['33467']['rank'], 1);
	}
*/


}

$tests = new AddictTests(1);
$tests->run();

?>