<?php 
error_reporting(E_ALL);
require_once('deck.php');
require_once('poker.php');

$deck = new Deck();
$deck->shuffleDeck();

$players = 6; //no of players on the table
$i = 1;

# Declare both community cards and private cards to be an array as well a combo for both to be an array too;
$comCards = array();
$privCards = array();
$combineCards = array();
$temp_score_details = array();

#distribute 2 cards for each player
for ( $count = 0; $count < $players; $count++ ):
	$privCards[$count] = array();
	array_push($privCards[$count], $deck->next());
	array_push($privCards[$count], $deck->next());
endfor;

#Lay 5 community cards on the table
while ( 5 > count($comCards) ) :
	array_push($comCards, $deck->next());
endwhile;

#create a combination of private cards and community cards for each players
for ( $count = 0; $count < $players; $count++ ):
	$combineCards[$count] = array_merge($comCards, $privCards[$count]);
	$temp_score_details[$count + 1] = Poker::checkCards( array_merge($comCards, $privCards[$count]) );
endfor;

?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<style>
			*{ font-family: "Arial";}
			body{ background: #eee;}
			.card{ display: inline-block; padding: 10px 5px; border: 1px solid #666; border-radius: 5px; -webkit-border-radius: 3px; -moz-border-radius: 3px; box-shadow: 2px 2px 2px #333; background: #FFF; }
			.community{ margin-right: 10px;}
			.community:last-child{ margin-right: 0; }
			.poker_table{ border: 1px solid #666; width: 400px; height: 240px; padding: 10px;  text-align: center; position: relative; border-radius: 10px; -webkit-border-radius: 10px; -moz-border-radius: 10px; background: #4ED640; }
			.center_table{ margin: auto; top: 40%; position: relative;}
			.float{ float: left;}
			.inline_block{ display: inline-block;}
			.clear{ clear: both;}
			.player_seat{ width:  200px; padding-left: 20px; }
			.player{ margin-right: 10px; margin-bottom: 30px; }
			.player h5{ margin: 0;}

		</style>
	</head>

	<body>
		<div>
			<h3>POKER HANDS RANKING</h3>
		</div>

		<div class="poker_table float">
			<div class="center_table">
				<?php foreach( $comCards as $comCard ) : ?>
				<div class="card community">
					<?=$comCard->rank.$comCard->suitSymbol;?>
				</div>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="player_seat float">
			<?php foreach( $privCards as $pCard) : ?>
			<div class="player inline_block">
				<h5>Player <?=$i++;?></h5>
				<?php foreach($pCard as $card):?>
				<div class="card">
					<?=$card->rank.$card->suitSymbol;?>
				</div>
				<?php endforeach;?>
			</div>
			<?php endforeach; ?>
		</div>
		<div class="clear"></div>

		<div>
			Ranking (1 - highest, 6 - lowest):<br/>
			<?php 
			sort($temp_score_details, SORT_NUMERIC);
			$score_board = array();

			foreach( $temp_score_details as $t):
				$score_board[] = poker::rankHands($max = max($temp_score_details ) );
				array_pop($temp_score_details);
			endforeach;
			echo "<ul class='ranking'>";
			for($i = 0; $i < count($score_board); $i++):
				echo "<li>Rank ".($i + 1).": ".$score_board[$i]."</li>";
			endfor;
			echo "</ul>";
			?>
		</div>
	</body>
</html>