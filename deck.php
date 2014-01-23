<?php
require_once('card.php');

class Deck
{
	protected $c_next = 0;
	protected $cards = array();

	public function __construct()
	{
		#create a deck of cards
		foreach ( range(0, 51) AS $selCard ) :
			array_push($this->cards, new Card( $selCard ) );
		endforeach;
	}

	public function shuffleDeck() //shuffle array cards
	{
		return shuffle($this->cards);  
	}

	public function next() //set and get next card on the deck
	{
		if ( !isset($this->cards[ $this->c_next ] ) ):
			return null;
		endif;
		return $this->cards[ $this->c_next++ ];
	}

	public function remaining() //returns the remaining number of cards on the deck
	{
		return ( count( $this->cards ) - $this->c_next);
	}

	public function show()
	{
		print_r($this->cards);
	}



}

?>