<?php
/*
	Movie Addict - IMDB Top 250 List - Kyle Conroy
*/

/*
	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses
	
/*******************************************************************************
 *                         MySQL Database Class
 *******************************************************************************
 *      Author:     Micah Carrick
 *      Email:      email@micahcarrick.com
 *      Website:    http://www.micahcarrick.com
 *
 *      File:       db.class.php
 *      Version:    1.0.4
 *      Copyright:  (c) 2005 - Micah Carrick 
 *                  You are free to use, distribute, and modify this software 
 *                  under the terms of the GNU General Public License.  See the
 *                  included license.txt file.
 *      
 *******************************************************************************
 *  VERION HISTORY:
 *  
 *      v1.0.4 [07.06.2005] - Added ability to add NULL values in update_array
 *                            and insert_array functions.
 *      v1.0.3 [05.10.2005] - Fixed bug in update_array funciton
 *      v1.0.2 [05.09.2005] - Fixed select_one function for queries returning 0
 *      v1.0.1 [04.28.2005] - Fixed bug in select_one function (returned null!)
 *      v1.0.0 [04.17.2005] - Initial Version
 *
 *******************************************************************************
 *  DESCRIPTION:
 *
 *      This class aids in MySQL database connectivity. It was written with
 *      my specific needs in mind.  It simplifies the database tasks I most
 *      often need to do thus reducing redundant code.  It is also written
 *      with the mindset to provide easy means of debugging erronous sql and
 *      data during the development phase of a web application.
 *
 *      The future may call for adding a slew of other features, however, at
 *      this point in time it just has what I often need.  I'm not trying to
 *      re-write phpMyAdmin or anything.  :)  Hope you find it  useful.
 *
 *      A screenshot and sample script can be found on my website.
 *
 *******************************************************************************
 */
require_once('config.php');

class AddictDatabase {
 
   var $host;               // mySQL host to connect to
   var $user;               // mySQL user name
   var $pw;                 // mySQL password
   var $db;                 // mySQL database to select

   var $db_link;            // current/last database link identifier
   var $auto_slashes;       // the class will add/strip slashes when it can
   
   function __construct($db = null) {
   
      // class constructor.  Initializations here.
      // Setup your own default values for connecting to the database here. You
      // can also set these values in the connect() function and using
      // the select_database() function.
      global $hostname;
      global $username;
      global $password;
      global $database;
      
      $this->host = $hostname;
      $this->user = $username;
      $this->pw = $password;
      if(!$db)
        $this->db = $database;   
      else
        $this->db = $db;
      $this->connect();
      $this->select_db();
   }

   function connect($host='', $user='', $pw='', $db='', $persistant=true) {
 
      // Opens a connection to MySQL and selects the database.  If any of the
      // function's parameter's are set, we want to update the class variables.  
      // If they are NOT set, then we're giong to use the currently existing
      // class variables.
      // Returns true if successful, false if there is failure.  
      
      if (!empty($host)) $this->host = $host; 
      if (!empty($user)) $this->user = $user; 
      if (!empty($pw)) $this->pw = $pw; 

 
      // Establish the connection.
      if ($persistant) 
         $this->db_link = mysql_pconnect($this->host, $this->user, $this->pw);
      else 
         $this->db_link = mysql_connect($this->host, $this->user, $this->pw);
 
      // Check for an error establishing a connection
      if (!$this->db_link) {
         $this->last_error = mysql_error();
         return false;
      } 
  
      // Select the database
      if (!$this->select_db($db)) return false;
 
      return $this->db_link;  // success
   }

   function select_db($db='') {
 
      // Selects the database for use.  If the function's $db parameter is 
      // passed to the function then the class variable will be updated.
 
      if (!empty($db)) $this->db = $db; 
      
      if (!mysql_select_db($this->db)) {
         $this->last_error = mysql_error();
         return false;
      }
 
      return true;
   }
   
   function query($sql) {
      
      // Performs an SQL query and returns the result pointer or false
      // if there is an error.
      
      $r = mysql_query($sql);
      if (!$r) {
	   	 print("MySQL Error: " . mysql_error() . "<br>");
	  }
      return $r;
   }
   
   	/* User Functions */
	function getUsers(){
		$row = $this->query("SELECT * FROM users");
		$users = array();
		while($result = mysql_fetch_array($row)){
				$user = array();
				$user['id'] = $result['user_id'];
				$user['count'] = $result['seen'];
				$users[$result['user_id']] = $user;
		}
		return $users;
	}
	
	function addUser($user_id){
		return $this->query("INSERT INTO users (user_id) VALUES($user_id)");
	}
	
	function deleteUser($user_id){
		return $this->query("DELETE FROM users WHERE user_id = $user_id");
	}
	
	function getUser($user_id){
		$user_id = (int) $user_id;
		$row = $this->query("SELECT * FROM users WHERE user_id = $user_id");
		$result = mysql_fetch_array($row);
		if($result){
			$user = array();
			$user['id'] = $result['user_id'];
			$user['count'] = $result['seen'];
			$user['percent'] = $result['percent'];
		} else
			$user = false;
		return $user;
	}
   
	/* User Movie Functions */
	function getUserMovies($user_id){
		$row = $this->query("SELECT * FROM users_movies WHERE user_id = $user_id");
		$movies = array();
		while($result = mysql_fetch_array($row)){
				$movies[] = $result['movie_id'];
		}
		return $movies;
	}

	function getUserMoviesRanked($user_id){
		$row = $this->query("SELECT users_movies.movie_id FROM users_movies JOIN movies ON users_movies.movie_id = movies.movie_id WHERE user_id = $user_id AND (imdb_pos IS NOT NULL OR afi_pos IS NOT NULL)");
		$movies = array();
		while($result = mysql_fetch_array($row)){
				$movies[] = $result['movie_id'];
		}
		return $movies;
	}
	
	function getMovieUsers($movie_id){
		$row = $this->query("SELECT * FROM users_movies WHERE movie_id = $movie_id");
		return $movies;
	}
	
	function updateUserMovieCount($user_id){
		$movies = self::getUserMoviesRanked($user_id);
		$total_count = count(self::getRankedMovies());
		$user_count = count($movies);
		$percent = round($user_count / $total_count * 100, 2);
		return $this->query("UPDATE users SET seen = $user_count, percent = $percent WHERE user_id = $user_id");
	}
	
	function addUserMovie($user_id, $movie_id){
		return $this->query("INSERT INTO  users_movies (user_id, movie_id) VALUES($user_id,$movie_id) ON DUPLICATE KEY UPDATE user_id=user_id");
	}
	
	function deleteUserMovie($user_id, $movie_id){
		return $this->query("DELETE FROM users_movies WHERE movie_id = $movie_id AND user_id = $user_id");
	}
	
	/* Movie Functions */
	function addMovie($movie_id, $title, $imdb_rank=NULL, $afi_rank=NULL){
		if(!$imdb_rank)
			$imdb_rank = "NULL";
		if(!$afi_rank)
			$afi_rank = "NULL";
		return $this->query("INSERT INTO movies (movie_id,title,imdb_pos,afi_pos) VALUES($movie_id, '$title', $imdb_rank, $afi_rank)");
	}
	
	function updateMovieTitle($movie_id, $title){
		return $this->query("UPDATE movies SET title = \"$title\" WHERE movie_id = $movie_id");
	}
	
	function resetIMDBRank(){
		return $this->query("UPDATE movies SET imdb_pos = NULL");
	}
	
	function resetAFIRank(){
		return $this->query("UPDATE movies SET afi_pos = NULL");
	}
	
	function updateIMDBRank($movie_id, $rank){
		return $this->query("UPDATE movies SET imdb_pos = $rank WHERE movie_id = $movie_id");
	}
	
	function updateAFIRank($movie_id, $rank){
		return $this->query("UPDATE movies SET afi_pos = $rank WHERE movie_id = $movie_id");
	}
	
	function updateNetflixInstant($movie_id, $bool){
	   if($bool)
	       $value = 1;
	   else
	       $value = 0;
       return $this->query("UPDATE movies SET instant = $value WHERE movie_id = $movie_id");
	}
	
    function updateNetflixId($movie_id, $netflix_id){
	   return $this->query("UPDATE movies SET netflix_id = $netflix_id WHERE movie_id = $movie_id");
	}
	
    function resetNetflixInstant(){
		return $this->query("UPDATE movies SET instant = 0");
	}
	
	function deleteMovie($movie_id){
		return $this->query("DELETE FROM movies WHERE movie_id = $movie_id");
	}
	
	function getMovie($movie_id){
		$row = $this->query("SELECT * FROM movies WHERE movie_id = $movie_id");
		$result = mysql_fetch_array($row);
		if($result){
			$movie = array();
			$movie['id'] = $result['movie_id'];
			$movie['netflix_id'] = $result['netflix_id']; 
			$movie['title'] = $result['title'];
			$movie['imdb_rank'] = $result['imdb_pos'];
			$movie['afi_rank'] = $result['afi_pos'];
			$movie['instant'] = $result['instant'];
		} else
			$movie = false;
		return $movie;
	}
	
	function getMovies(){
		return self::movieQuery("SELECT * FROM movies");
	}
	
	function getRankedMovies(){
		return self::movieQuery("SELECT * FROM movies WHERE imdb_pos IS NOT NULL OR afi_pos IS NOT NULL");
	}
	
	function getIMDBMovies(){
		return self::movieQuery("SELECT * FROM movies WHERE imdb_pos IS NOT NULL ORDER BY imdb_pos");
	}
	
	function getAFIMovies(){
		return self::movieQuery("SELECT * FROM movies WHERE afi_pos IS NOT NULL ORDER BY afi_pos");
	}
	
	private function movieQuery($sql){
		$row = $this->query($sql);
		$movies = array();
		while($result = mysql_fetch_array($row)){
				$movie = array('id' => $result['movie_id'], 
							   'title' => $result['title'],
							   'imdb_rank' => $result['imdb_pos'],
							   'afi_rank' => $result['afi_pos'],
							   'instant' => $result['instant'],
							   'netflix_id' => $result['netflix_id']);
				$movies[$result['movie_id']] = $movie;
		}
		return $movies;
	}
   
} 

?>







