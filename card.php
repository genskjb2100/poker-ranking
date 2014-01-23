<?php

class Card {

	public $id = -1;
	public $suit = '';  //class variable for the card's suit
	public $suitSymbol= '';
	public $rank = '';  //class variable for the rank
	public $value = ''; //var for value of rank

	public function __construct( $c_selCard ) //create a constuctor to set properties for the selected Card
	{
		#Make sure that our card is a valid integer
		$selCard = (int)$c_selCard%52;

		#Determines the suit of the card by dividing the selected card by the number of ranks in order to get what suit it belongs to and make sure to round of the quotient to the lowest int value
		$suit = floor($selCard/13);			
		
		#Determines the card's rank by getting the mod int value of the card
		$rank = $selCard%13;

		#set the array containers for the values of the card's main properties which are suits and ranks and set them as static
		static $arr_suits = array('clubs', 'spades', 'hearts', 'diamonds');
		static $arr_suits_symbol = array('&clubs;','&spades;','&hearts;','&diams;');
		static $arr_ranks = array('A', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K');
		static $arr_rank_vals = array(14, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13);
		$this->id = $selCard;
		$this->suit = $arr_suits[$suit];
		$this->rank = $arr_ranks[$rank];
		$this->value = $arr_rank_vals[$rank];
		$this->suitSymbol = $arr_suits_symbol[$suit];
	}

	public function activeCard() 
	{
		return $this->rank.' of '.$this->suit; //return the activeCard's name (RANK + SUIT)
	}

}
?>