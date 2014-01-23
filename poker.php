<?php 
class Poker
{
	#declare class variables
	public $cards = array();
	public $rank_values = array();
	public $count_ranks = array();
	public $suits = array();
	public $count_suits = array();
	public $player_win_order = array();

	#construct the 7 cards for each player
	public function __construct($combineCards) {
		$this->cards = $combineCards;
		$temp = array();
		foreach ( $combineCards AS $c_Card ) :
			$temp[] = array( 'value' => $c_Card->value, 'suit' => $c_Card->suit );
		endforeach;

		$arrCards = self::objToArray($temp); //make the obj into array for easier manipulation

		#extract the card ranks and order the ranks descending
		$this->rank_values = $arrCards['value'];
		arsort($this->rank_values);
		$this->count_ranks = array_count_values($this->rank_values);

		$this->suits = $arrCards['suit'];
		arsort($this->suits);
		$this->count_suits = array_count_values($this->suits);
		arsort($this->count_suits);

	}

	public static function objToArray($objArr)
	{
		$temp = array();
		foreach ( $objArr AS $key1 => $value1 ) :
			foreach ( $value1 AS $key2 => $value2 ) :
				$temp[$key2][$key1] = $value2;
			endforeach;
		endforeach;
		
		return $temp;
	}

	private function _checkCards() {
		#hand Categories
		$handCatOrder = array( 9 => 'royal_flush', 8 => 'straight_flush', 7 => 'four_of_a_kind', 6 => 'full_house', 5 => 'flush', 4 => 'straight', 3 => 'three_of_a_kind', 2 => 'two_pair',	1 => 'one_pair', );
		
		$handRank = 0;
		#iterate to check the highest possible combination of cards for each player
		foreach ( $handCatOrder AS $handVal => $handCall ) :
			if ( null !== ($details = $this->$handCall=call_user_func(array($this, $handCall))) ) :
				$handRank = $handVal;
				break;
			endif;
		endforeach;
		
		if ( 1 > $handRank) :
			$handRank = 0;
			$details = '';	
			//iterate through rank_values to determine what are the ranks for each combination
			foreach ( $this->rank_values AS $i ):
				if ( 10 > strlen($details) ):
					$details .= self::padleft($i);
				endif;
				if ( 10 <= strlen($details) ):
					break;
				endif;
			endforeach;
		endif;
		return ($handRank.'.'.$details);
	}

	public static function checkCards($combineCards, &$con = null)
	{
		$con = new self($combineCards);
		return $con->_checkCards();
		//echo "<br/>";
	}

	public static function rankHands($p_cards)
	{
		$handDetails = explode('.', $p_cards, 2);
		static $rankText = array(2 => 'Twos', 'Threes', 'Fours', 'Fives', 'Sixes', 'Sevens', 'Eights', 'Nines', 'Tens', 'Jacks', 'Queens', 'Kings', 'Aces');
		static $ranks = array(2 => '2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K', 'A');
		$hand = '';
		$details = isset($handDetails[1]) ? $handDetails[1] : '';

		switch( (int)$handDetails[0] )
		{
			case 9: $hand = "Royal flush of ".$details; break;

			case 8: $hand = "Straight flush of".ucfirst(substr($details, 2)).' - '.$rankText[(int)substr($details, 0, 2)].' highest'; break;

			case 7: $hand = "Four of a kind -".$rankText[(int)substr($details, 0, 2)].''; break;			

			case 6: $hand = "Full house of ".$rankText[(int)substr($details, 0, 2)].' with'.$rankText[(int)substr($details, 2, 2)].''; break;

			case 5: $hand = "Flush of". ucfirst(substr($details, 10) ).' - '.$rankText[(int)substr($details, 0, 2)].' highest'; break;

			case 4: $hand = "Straight - ".$rankText[(int)substr($details, 0, 2)].' highest'; break;

			case 3: $hand = "Three of a kind - ".$rankText[(int)substr($details, 0, 2)]; break;

			case 2: $hand = "Two pairs - ".$rankText[(int)substr($details, 0, 2)].' and '.$rankText[(int)substr($details, 2, 2)].''; break;

			case 1: $hand = "One pair - ".$rankText[(int)substr($details, 0, 2)].''; break;

			default:
				$kickers = array($ranks[(int)substr($details, 0, 2)]);
				if ( 0 < ($c_Card = (int)substr($details, 2, 2)) ) {
					$kickers[] = $ranks[$c_Card];
				}
				if ( 0 < ($c_Card = (int)substr($details, 4, 2)) ) {
					$kickers[] = $ranks[$c_Card];
				}
				if ( 0 < ($c_Card = (int)substr($details, 6, 2)) ) {
					$kickers[] = $ranks[$c_Card];
				}
				if ( 0 < ($c_Card = (int)substr($details, 8, 2)) ) {
					$kickers[] = $ranks[$c_Card];
				}
				$hand = 'High Cards - '.implode(', ', $kickers).'';
			break;

		}
		return $hand;
	}

	#make sure each card combination is at least two digits for easier manipulation
	public static function padleft($string) 
	{
		return str_pad(''.$string, 2, '0', STR_PAD_LEFT);
	}

	# check whether card combination qualifies for royal flush category but already predetermined on func straight_flush with return value not null
	public function royal_flush() {
		$fn = __FUNCTION__;

		if ( isset($this->$fn) ):
			return $this->$fn;
		endif;

		$sf = $this->straight_flush();

		if ( $sf != null && '14' === substr($sf, 0, 2) ):
			$this->$fn = substr($sf, 2);

			return $this->$fn;		
		endif;

		return null;
	}
	# check whether card combination qualifies for the straight flush category but already predetermined on func's straight and flush with return values not null
	public function straight_flush() {
		$fn = __FUNCTION__;
		if ( isset($this->$fn) ) :
			return $this->$fn;
		endif;
		#check if card combo is a flush
		if ( ($suit = $this->flush(true)) == null) :
			return null;
		endif;

		$selCard = array();
		
		foreach ( $this->rank_values AS $card => $rank ) :
			if ( $suit == $this->suits[$card] ) :
				$selCards[] = $rank;
			endif;
		endforeach;

		if ( ($biggest = $this->straight($selCards)) != null ) :
			$this->$fn = $biggest.$suit;
			return $this->$fn;
		endif;
		return null;
	}
	
	#check whether a card combination qualifies for four_of_a_kind
	public function four_of_a_kind() {
		$fn = __FUNCTION__;
		
		if ( isset($this->$fn) ) :
			return $this->$fn;
		endif;

		foreach ( $this->count_ranks AS $rank => $count ) :
			
			if ( 4 <= $count ) :
				$details = self::padleft($rank);
				foreach ( $this->rank_values AS $r ) :
					if ( $r != $count ) :
						$details .= self::padleft($r);
						$this->$fn = $details;
						return $details;
					endif;
				endforeach;
			endif;
		endforeach;

		return null;
	}
	
	# check whether the card combination is a fullhouse but already predetermined by func's three_of_a_kind and one_pair having return values not null
	public function full_house() {
		$fn = __FUNCTION__;
		
		if ( isset($this->$fn) ) :
			return $this->$fn;
		endif;

		if ( null !== ( $trip = $this->three_of_a_kind($v)) ) :
			$temp = $this->count_ranks;
			unset($this->count_ranks[$v]);

			if ( null !== ($pair = $this->one_pair($v)) ) :
				
				$this->count_ranks = $temp;

				$this->$fn = substr($trip, 0, 2).substr($pair, 0, 2);
				return $this->$fn;
			endif;
			
			$this->count_ranks = $temp;

		endif;
		return null;
	}
	
	#test if card combination is of the same suit or is Flushes
	public function flush($flag = false) {
		$fn = __FUNCTION__;
		if ( isset($this->$fn) ) :
			return $this->$fn;
		endif;

		if (reset($this->count_suits) >= 5 ):
			$suit = key($this->count_suits);
			
			if ( $flag ) :
				return $suit;
			endif;

			$details = '';
			
			foreach ( $this->rank_values AS $card => $val ) :
				if ( $suit == $this->suits[$card] && 10 > strlen($details) ) :
					$details .= self::padleft($val);
				endif;
				if ( 10 <= strlen($details) ) :
					break;
				endif;
			endforeach;

			$this->$fn = $details.$suit;
			return $this->$fn;
		endif;

		return null;
	}

	# check whether the cards qualify for straight 
	public function straight($arrVal = null) {
		$fn = __FUNCTION__;
		if ( isset($this->$fn) ):
			return $this->$fn;
		endif;
		
		$arrValues = is_array($arrVal) ? $arrVal : array_keys($this->rank_values);

		if ( 5 > count($arrValues) ):
			return null;
		endif;

		for ( $inc = 0; $inc <= count($arrValues) - 5; $inc++ ):
			$biggest = $arrValues[$inc]; 
			$prev = $arrValues[$inc];
			$flag = true;

			for ( $j = $inc + 1; $j < $inc + 5; $j++ ) :
				if ( $arrValues[$j] != $prev - 1 ) :
					$flag = false;
					break;
				endif;
				$prev = $arrValues[$j];
			endfor;
			if ( $flag ):
				$this->$fn = self::padleft($biggest);
				return $this->$fn;
			endif;
		endfor;

		# assure that the card straight combination with ace having the lowest value if it's highest straight card rank value is 5
		if ( in_array(14, $arrValues) && in_array(2, $arrValues) && in_array(3, $arrValues) && in_array(4, $arrValues) && in_array(5, $arrValues) ) :
			$this->$fn = '05';
			return $this->$fn;
		endif;

		return null;
	}
	

	# test combination if it is 3 of a kind
	public function three_of_a_kind(&$rank_count_val = null) {
		$fn = __FUNCTION__;
		if ( isset($this->$fn) ) :
			return $this->$fn;
		endif;
		
		foreach ( $this->count_ranks AS $val => $count ) :
			if ( 3 <= $count ) :
				$rank_count_val = $val;
				$details = self::padleft($val);
				
				foreach ( $this->rank_values AS $i ) :
				
					if ( $val != $i && 6 > strlen($details) ) :
						$details .= self::padleft($i);
					endif;
				
					if ( 6 <= strlen($details) ):
						break;
					endif;
				endforeach;
				
				$this->$fn = $details;
				return $details;
			endif;	
		endforeach;
		return null;
	}

	#test combination for 2 pairs
	public function two_pair(&$val1 = null, &$val2 = null) {
		$fn = __FUNCTION__;
		
		if ( isset($this->$fn) ) :
			return $this->$fn;
		endif;
		
		$details = '';
		$t_val1 = 0;
		$t_val2 = 0;
		
		foreach ( $this->count_ranks AS $val => $count ):
			if ( 2 <= $count && 4 > strlen($details) ):
				$details .= self::padleft($val);

				if ( $t_val1 == 0 ) :
				 	$t_val1 = $val;
				else :
					$t_Val2 = $val;
				endif;
			endif;
		endforeach;
		
		if ( 4 == strlen($details) ) :
			foreach ( $this->rank_values AS $i ) :
				if ( $t_val1 != $i && $t_val2 != $i ) :
					$details .= self::padleft($i);
					break;
				endif;
			endforeach;
			$this->$fn = $details;
			return $details;
		endif;

		return null;
	}

	#test if the combination has at least 1 pair
	public function one_pair(&$rank_count_val = null) {
		$fn = __FUNCTION__;
		if ( isset($this->$fn) ) :
			return $this->$fn;
		endif;
		
		foreach ( $this->count_ranks AS $val => $count ):
			if ( 2 <= $count ) :
				$rank_count_val = $val;
				$details = self::padleft($val);
				
				foreach ( $this->rank_values AS $i ) :
					if ( $val != $i && 8 > strlen($details) ) :
						$details .= self::padleft($i);
					endif;

					if ( 8 <= strlen($details) ) :
						break;
					endif;

				endforeach;

				$this->$fn = $details;
				return $details;
			endif;
		endforeach;
		
		return null;
	}

}

?>