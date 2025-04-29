<?php
session_start();

define('kFNAME', 'rehack');  // define PHP constant for this page-address

function cel2top($cellnum)
{
    $x = (int) ($cellnum / 7);
    return ($x * 40) + 27;
}

function cel2left($cellnum)
{
    $x = (int) ($cellnum % 7);
    return ($x * 40) + 32;
}

function targcel2top($cellnum)
{
    return cel2top($cellnum) + 4;
}

function targcel2left($cellnum)
{
    return cel2left($cellnum) + 4;
}

function genValidCells($b)
{
    $a[0] = null;
    $cnt = 0;
    $ix = 0;
    $bord = getBord();
    foreach ($bord[$b] as $i) {
        switch ($i) {
            case 'w':
            case 'o':
            case 'g':
            case 'y':
            case 'p':
            case '_':
            case '':
                $a[$ix++] = $cnt;
        }
        $cnt++;
    }
    return $a;
}

function initBord()
{
    $rf0 = array(0 => 'x', 'x', 'x', '', '', '', 'x', 'x', '', '', 'y', '', '', 'x', '', '', 'x', '', 'w', '', '', '', 'g', '', '_', '', 'x', '', '', '', 'p', 'x', 'o', '', '', '', '', '', '', '', '', '');
    $rf3 = array(0 => 'x', '', '', '', '', '', 'x', '', '', '', 'o', 'x', '', '', '', '', 'x', '', 'g', '', '', '', 'w', '', '_', '', '', '', 'x', '', 'p', '', 'y', '', 'x', 'x', '', '', 'x', '', '', 'x');

    $bord[0] = $rf0;
    $bord[1] = $rf3;

    if (isset($_SESSION['bord'])) {
        unset($_SESSION['bord']);
    }

    $_SESSION['bord'] = $bord;
    return $bord;
}

function getBord()
{
    if (!isset($_SESSION['bord'])) {
        $bord = initBord();
    } else {
        $bord = $_SESSION['bord'];
    }
    return $bord;
}

function INITb0()
{
    $b0 = genValidCells(0);
    foreach ($b0 as $el) {
        echo "<span style='left:" . targcel2left($el) . '; top:' . targcel2top($el) . "; width:0px; height: 0px; position:absolute; z-index:501;'> <a href='" . kFNAME . '.php?act=placeshrimp&b=0&cell=' . $el . "'>[+]</a></span>" . PHP_EOL;
    }
}

function INITb3()
{
    $b3 = genValidCells(1);
    foreach ($b3 as $el) {
        echo "<span style='left:" . targcel2left($el) . '; top:' . targcel2top($el) . "; width:0px; height: 0px; position:absolute; z-index:501;'> <a href='" . kFNAME . '.php?act=placeshrimp&b=1&cell=' . $el . "'>[+]</a></span>" . PHP_EOL;
    }
}

function getTargetSymbol($board, $cell)
{
    // Convert cell number to row and column
    $row = (int) ($cell / 7);
    $col = $cell % 7;

    // Get the symbol at this position
    return $board[$row][$col];
}

function isValidShrimpPosition($board, $cell)
{
    $symbol = getTargetSymbol($board, $cell);
    return ($symbol != 'x' && $symbol != '');
}

function placeShrimp($board, $cell)
{
    if (isValidShrimpPosition($board, $cell)) {
        $row = (int) ($cell / 7);
        $col = $cell % 7;
        $board[$row][$col] = 's';  // 's' represents a shrimp
        return $board;
    }
    return false;
}

// Initialize or get the current board state
$bord = getBord();

// Handle shrimp placement if requested
if (isset($_GET['act']) && $_GET['act'] == 'placeshrimp' && isset($_GET['cell']) && isset($_GET['b'])) {
    $cell = (int) $_GET['cell'];
    $board = (int) $_GET['b'];
    $bord[$board][$cell] = 's';  // Place shrimp
    $_SESSION['bord'] = $bord;
    header('Location: ' . kFNAME . '.php');
    exit;
}

// Handle board reset
if (isset($_GET['act']) && $_GET['act'] == 'reset') {
    unset($_SESSION['bord']);
    $bord = getBord();
    header('Location: ' . kFNAME . '.php');
    exit;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML LANG="en">

<HEAD>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <TITLE>SpielByWeb: Reef Encounter </TITLE>
  <LINK REL="stylesheet" HREF="pbw.css" TYPE="text/css" />
</HEAD>

<BODY>
  <script language="JavaScript" type="text/javascript">
    var ol_width = 60;
  </script>
  <DIV ID="overDiv" STYLE="position:absolute; visibility:hidden; z-index:1000;"></DIV>
  <SCRIPT LANGUAGE="JavaScript" type="text/javascript" SRC="overlib.js">
    <!-- overLIB (c) Erik Bosrup 
    -->
  </SCRIPT>
  <!--  This is the container for the whole page. -->
  <DIV CLASS="normal_text">
    <TABLE CELLPADDING="3" CELLSPACING="0" WIDTH="100%">
      <!-- First table gives us the header, all the basic info like the name of the game and log in name and links to the rest of the site  -->
      <TR>
        <TD CLASS="menu">
          <TABLE CELLPADDING="0" CELLSPACING="0" WIDTH="100%">
            <TR VALIGN="BOTTOM">
              <TD COLSPAN="4">
                <TABLE CELLPADDING="0" CELLSPACING="0" WIDTH="100%">
                  <TR VALIGN="BOTTOM">
                    <TD><SPAN CLASS="h2">SpielByWeb: Reef Encounter </SPAN></TD>
                    <TD ALIGN="RIGHT">Logged in as: <B>englebert</B></TD>
                  </TR>
                </TABLE>
              </TD>
            </TR>
            <TR VALIGN="TOP">
              <TD COLSPAN="4" BGCOLOR="0066CC" HEIGHT="2"></TD>
            </TR>
            <TR VALIGN="TOP">
              <TD COLSPAN="4"><IMG SRC="images/tp.gif" WIDTH="800" HEIGHT="1" ALT="" /></TD>
            </TR>
            <TR VALIGN="TOP" ALIGN="CENTER">
              <TD CLASS="small_text_8" ALIGN="LEFT"><A CLASS="menu" HREF="index.php">Home</A> | <A CLASS="menuRed" HREF="forum/faq.php">FAQ</A></TD>
              <TD CLASS="small_text_8"><A CLASS="menu" HREF="create.php">Create Game</A> | <A CLASS="menu" HREF="games.php">Games List</A> | <A CLASS="menuRed" HREF="yourgames.php">Your Games</A> <A HREF="game.php?games_id=98586" CLASS="menuRed">(1)</A> | <A CLASS="menu" HREF="users.php">Stats</A></TD>
              <TD CLASS="small_text_8"><A CLASS="menu" HREF="forum/">Forum</A> (<A HREF="forum/search.php?search_id=newposts">11 new posts</A>) | <A CLASS="menu" HREF="forum/viewforum.php?f=1">Updates</A> | <a class="menuRed" href="donate.php">Donate</a></TD>
              <TD ALIGN="RIGHT" CLASS="small_text_8"><A CLASS="menu" HREF="forum/profile.php?mode=viewprofile&amp;u=93309">Profile</A> | <A CLASS="menu" HREF="forum/profile.php?mode=editprofile">Edit</A> | <A CLASS="menu" HREF="vacation.php">Going Away?</A> | <A CLASS="menu" HREF="login.php?logout=true">Log out</A></TD>
            </TR>
          </TABLE>
        </TD>
      </TR>
      <TR>
        <!-- Empty table tr tag. -->
        <TD></TD>
      </TR>
      <!-- This tag holds the rest of the page. -->
      <TR>
        <TD>
          <BR />
          <!-- The table that begins the part of the page associated with the game. -->
          <TABLE CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
            <TR>
              <TD COLSPAN="3">
                <TABLE CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
                  <TR VALIGN="BOTTOM">
                    <!-- The game number and name, round # -->
                    <TD>Game 985FN: <SPAN CLASS="h3"><A HREF="http://www.spielbyweb.com/game.php?games_id=98538">REhack</A></SPAN></TD>
                    <TD ALIGN="RIGHT"><B>Round 1</B></TD>
                  </TR>
                </TABLE>
              </TD>
            </TR>
            <TR>
              <TD COLSPAN="3" BGCOLOR="0066CC" HEIGHT="1"></TD>
            </TR>
            <TR>
              <TD COLSPAN="3" HEIGHT="1"></TD>
            </TR>
            <!-- ############################################################
           <!--  Links to gamelog, messages, notes,  bug-report, etc. 			-->
            <!-- ############################################################  	-->
            <TR VALIGN="TOP">
              <TD WIDTH="40%"><A HREF="gamelog.php?games_id=98586">Gamelog</A> | <A HREF="messages.php?games_id=98586">Messages</A> | <A HREF="notepad.php?games_id=98586">Notepad</A> | <A HREF="/forum/viewforum.php?f=9">Bug Report</A></TD>
              <TD ALIGN="CENTER"><A CLASS="menu" HREF="http://www.spielbyweb.com/rules.php?game=7">Rules</A> | <A CLASS="menu" HREF="http://www.boardgamegeek.com/viewitem.php3?gameid=12962">BoardGameGeek</A> | <A CLASS="menu" HREF="http://www.funagain.com/cgi-bin/funagain/14942?;;SBYW">Buy at Funagain</A></TD>
              <TD ALIGN="RIGHT"><SPAN CLASS="small_text">Game Started on Thu Feb 07, 2013 3:52 pm<BR />Game Updated on Thu Feb 07, 2013 4:09 pm</SPAN></TD>
            </TR>
            <TR>
              <TD>&nbsp;</TD>
            </TR>
            <!-- ############################################################
		   <!-- 	INPUT ERROR MSG 			 INPUT ERROR MSG
		   <!-- ############################################################  -->
            <TR>
              <!--TD ALIGN="CENTER" COLSPAN="3" BGCOLOR="FFAAAA"><BR /><B>Input error -- please retry!</B><BR /><BR /></TD-->
            </TR>
            <TR>
              <TD COLSPAN="3">&nbsp;</TD>
            </TR>
            <TR>
              <!-- This tag is for the gameboard only, it contains inputs and a bunch of gameboard pieces  -->
              <TD COLSPAN="3">
                <TABLE WIDTH="100%" CELLPADDING="0" CELLSPACING="0">
                  <!-- This area holds the player name, info on each player's larva cubes, your hand of shrimp, polyp tiles, larva cubes, and whatever is inside your parrot fishy.  -->
                  <TR VALIGN="TOP">
                    <TD WIDTH="50%">
                      <TABLE CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
                        <TR>
                          <TD CLASS="border">
                            <TABLE CELLSPACING="0" CELLPADDING="3" WIDTH="100%">
                              <TR ALIGN="CENTER">
                                <TD CLASS="thin" BGCOLOR="#0066CC"><SPAN CLASS="yellow_text_bold">Player</SPAN></TD>
                                <TD CLASS="thin" BGCOLOR="#0066CC"><SPAN CLASS="yellow_text_bold">Consumed<BR />Polyp Tiles</SPAN></TD>
                                <TD CLASS="thin" BGCOLOR="#0066CC"><SPAN CLASS="yellow_text_bold">Shrimp<BR />Eaten</SPAN></TD>
                                <TD CLASS="thin" BGCOLOR="#0066CC"><SPAN CLASS="yellow_text_bold">Initial larva cubes</SPAN></TD>
                              </TR>
                              <TR ALIGN="CENTER" VALIGN="TOP" BGCOLOR="#FFFF70">
                                <TD CLASS="thin" ALIGN="LEFT">
                                  <TABLE CELLSPACING="0" CELLPADDING="0">
                                    <TR>
                                      <TD><IMG SRC="images/P.gif" WIDTH="16" HEIGHT="16" ALT="P" ALIGN="ABSMIDDLE" />&nbsp;</TD>
                                      <TD><A HREF="forum/profile.php?mode=viewprofile&amp;u=93309&amp;g=7" CLASS="player_ref">englebert</A></TD>
                                    </TR>
                                  </TABLE>
                                </TD>
                                <TD CLASS="thin">&nbsp;</TD>
                                <TD CLASS="thin">&mdash;</TD>
                                <TD CLASS="thin">
                                  <TABLE CELLSPACING="0" CELLPADDING="0">
                                    <TR>
                                      <TD>1x</TD>
                                      <TD><IMG BORDER="1" SRC="game/reef/images/l3.gif" ALT="[p]" WIDTH="16" HEIGHT="16" ALIGN="ABSMIDDLE" /></TD>
                                      <TD>&nbsp;</TD>
                                      <TD>1x</TD>
                                      <TD><IMG BORDER="1" SRC="game/reef/images/l4.gif" ALT="[g]" WIDTH="16" HEIGHT="16" ALIGN="ABSMIDDLE" /></TD>
                                      <TD>&nbsp;</TD>
                                    </TR>
                                  </TABLE>
                                </TD>
                              </TR>
                              <TR ALIGN="CENTER" VALIGN="TOP">
                                <TD CLASS="thin" ALIGN="LEFT">
                                  <TABLE CELLSPACING="0" CELLPADDING="0">
                                    <TR>
                                      <TD><IMG SRC="images/G.gif" WIDTH="16" HEIGHT="16" ALT="G" ALIGN="ABSMIDDLE" />&nbsp;</TD>
                                      <TD><A HREF="forum/profile.php?mode=viewprofile&amp;u=93304&amp;g=7" CLASS="player_ref">fozwick</A></TD>
                                    </TR>
                                  </TABLE>
                                </TD>
                                <TD CLASS="thin">&nbsp;</TD>
                                <TD CLASS="thin">&mdash;</TD>
                                <TD CLASS="thin">
                                  <TABLE CELLSPACING="0" CELLPADDING="0">
                                    <TR>
                                      <TD>1x</TD>
                                      <TD><IMG BORDER="1" SRC="game/reef/images/l0.gif" ALT="[w]" WIDTH="16" HEIGHT="16" ALIGN="ABSMIDDLE" /></TD>
                                      <TD>&nbsp;</TD>
                                      <TD>1x</TD>
                                      <TD><IMG BORDER="1" SRC="game/reef/images/l3.gif" ALT="[p]" WIDTH="16" HEIGHT="16" ALIGN="ABSMIDDLE" /></TD>
                                      <TD>&nbsp;</TD>
                                    </TR>
                                  </TABLE>
                                </TD>
                              </TR>
                            </TABLE>
                          </TD>
                        </TR>
                      </TABLE>
                      <BR />
                    </TD>
                    <TD WIDTH="10"></TD>
                    <TD>
                      <TABLE CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
                        <TR>
                          <TD CLASS="border">
                            <TABLE CELLSPACING="0" CELLPADDING="3" WIDTH="100%">
                              <TR ALIGN="CENTER">
                                <TD CLASS="border" BGCOLOR="#0066CC"><SPAN CLASS="yellow_text_bold">Behind Your Screen</SPAN></TD>
                              </TR>
                              <TR>
                                <TD CLASS="border">
                                  <TABLE>
                                    <TR>
                                      <TD ALIGN="RIGHT">Polyp Tiles:</TD>
                                      <TD></TD>
                                      <TD>
                                        <TABLE CELLSPACING="0" CELLPADDING="0">
                                          <TR>
                                            <TD>1x</TD>
                                            <TD><IMG BORDER="1" SRC="game/reef/images/p1.jpg" ALT="[y]" WIDTH="16" HEIGHT="16" ALIGN="ABSMIDDLE" /></TD>
                                          </TR>
                                        </TABLE>
                                      </TD>
                                      <TD>
                                        <TABLE CELLSPACING="0" CELLPADDING="0">
                                          <TR>
                                            <TD>1x</TD>
                                            <TD><IMG BORDER="1" SRC="game/reef/images/p2.jpg" ALT="[o]" WIDTH="16" HEIGHT="16" ALIGN="ABSMIDDLE" /></TD>
                                          </TR>
                                        </TABLE>
                                      </TD>
                                      <TD>
                                        <TABLE CELLSPACING="0" CELLPADDING="0">
                                          <TR>
                                            <TD>3x</TD>
                                            <TD><IMG BORDER="1" SRC="game/reef/images/p3.jpg" ALT="[p]" WIDTH="16" HEIGHT="16" ALIGN="ABSMIDDLE" /></TD>
                                          </TR>
                                        </TABLE>
                                      </TD>
                                      <TD></TD>
                                    </TR>
                                    <TR>
                                      <TD ALIGN="RIGHT">Larva Cubes:</TD>
                                      <TD></TD>
                                      <TD></TD>
                                      <TD></TD>
                                      <TD>
                                        <TABLE CELLSPACING="0" CELLPADDING="0">
                                          <TR>
                                            <TD>1x</TD>
                                            <TD><IMG BORDER="1" SRC="game/reef/images/l3.gif" ALT="[p]" WIDTH="16" HEIGHT="16" ALIGN="ABSMIDDLE" /></TD>
                                          </TR>
                                        </TABLE>
                                      </TD>
                                      <TD>
                                        <TABLE CELLSPACING="0" CELLPADDING="0">
                                          <TR>
                                            <TD>1x</TD>
                                            <TD><IMG BORDER="1" SRC="game/reef/images/l4.gif" ALT="[g]" WIDTH="16" HEIGHT="16" ALIGN="ABSMIDDLE" /></TD>
                                          </TR>
                                        </TABLE>
                                      </TD>
                                    </TR>
                                    <TR>
                                      <!-- ############################################################
		   <!-- 	SHRIMP DISPLAY  			SHRIMP DISPLAY
		   <!-- ############################################################  -->
                                      <TD ALIGN="RIGHT">Shrimp:</TD>
                                      <TD></TD>
                                      <TD COLSPAN="15"><IMG SRC="game/reef/images/sP.gif" /><IMG SRC="game/reef/images/sP.gif" /><IMG SRC="game/reef/images/sP.gif" /><IMG SRC="game/reef/images/sP.gif" /></TD>
                                    </TR>
                                  </TABLE>
                                </TD>
                              </TR>
                            </TABLE>
                          </TD>
                        </TR>
                      </TABLE>
                      <BR />
                    </TD>
                    <TD WIDTH="10"></TD>
                    <TD>
                      <TABLE CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
                        <TR>
                          <TD CLASS="border">
                            <TABLE CELLSPACING="0" CELLPADDING="3" WIDTH="100%">
                              <TR ALIGN="CENTER">
                                <TD CLASS="border" BGCOLOR="#0066CC"><SPAN CLASS="yellow_text_bold">In Your Parrotfish</SPAN></TD>
                              </TR>
                              <TR>
                                <TD CLASS="border">
                                  <TABLE WIDTH="100%">
                                    <TR ALIGN="CENTER">
                                      <TD>
                                        <TABLE CELLSPACING="0" CELLPADDING="0">
                                          <TR>
                                            <TD>1x</TD>
                                            <TD><IMG BORDER="1" SRC="game/reef/images/p3.jpg" ALT="[p]" WIDTH="16" HEIGHT="16" ALIGN="ABSMIDDLE" /></TD>
                                            <TD>&nbsp;</TD>
                                          </TR>
                                        </TABLE>
                                      </TD>
                                    </TR>
                                  </TABLE>
                                </TD>
                              </TR>
                            </TABLE>
                          </TD>
                        </TR>
                      </TABLE>
                      <BR />
                    </TD>
                  </TR>





                  <!-- ############################################################
		   <!-- 	ACTIONS INTERFACE  			ACTIONS INTERFACE
		   <!-- ############################################################  -->
                  <TR>
                    <TD COLSPAN="5">
                      <TABLE CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
                        <TR>
                          <TD CLASS="borderred">
                            <TABLE CELLSPACING="0" CELLPADDING="3" WIDTH="100%">
                              <TR ALIGN="CENTER">
                                <TD CLASS="borderred" BGCOLOR="#FF3333"><SPAN CLASS="yellow_text_bold">Choose an Action</SPAN></TD>
                              </TR>
                              <TR>
                                <TD CLASS="borderred" BGCOLOR="#FFFFBB">
                                  <TABLE WIDTH="100%">
                                    <!-- 	ACTION 1 -- Eat a Shrimp  -->
                                    <TR>
                                      <TD><IMG SRC="game/reef/images/a1d.jpg" WIDTH="50" HEIGHT="32" ALIGN="LEFT" /> <B>Action 1</B>:</B> Eat one coral and a shrimp with your parrotfish<BR />(once at start of turn only)</TD>
                                    </TR>
                                  </TABLE>
                                </TD>
                              </TR>
                              <TR>
                                <TD CLASS="borderred" BGCOLOR="#FFFFBB">
                                  <TABLE WIDTH="100%">
                                    <TR>
                                      <!-- 	ACTION 2 -- Grow some Coral (#1)  -->
                                      <TD WIDTH="50%"><!--A HREF="game.php?games_id=98586&amp;uniq_id=2883274651551711703533&amp;input=a&amp;sel=2"--><IMG SRC="game/reef/images/a2d.jpg" WIDTH="50" HEIGHT="32" ALIGN="LEFT" BORDER="0" /> <B>Action 2:</B></A></B> Play a larva cube and polyp tiles<BR />(only once per turn)</TD>
                                      <!-- 	ACTION 6 -- Exchange a Consumed for a Cube  -->
                                      <TD WIDTH="50%"><IMG SRC="game/reef/images/a6d.jpg" WIDTH="50" HEIGHT="32" ALIGN="LEFT" /> <B>Action 6</B>:</B> Exchange a consumed polyp tile for a larva cube of the same colour (larva cube must be played immediately)</TD>
                                    </TR>
                                    <TR>
                                      <!-- 	ACTION 3 -- Grow some Coral (#2)  -->
                                      <TD WIDTH="50%"><IMG SRC="game/reef/images/a3d.jpg" WIDTH="50" HEIGHT="32" ALIGN="LEFT" /> <B>Action 3</B>:</B> Play a second larva cube and polyp tiles<BR />(only once per turn)</TD>
                                      <!-- 	ACTION 7 -- Play a Cylinder  	-->
                                      <TD WIDTH="50%"><IMG SRC="game/reef/images/a7d.jpg" WIDTH="50" HEIGHT="32" ALIGN="LEFT" /> <B>Action 7</B>:</B> Acquire and play an alga cylinder</TD>
                                    </TR>
                                    <TR>
                                      <!-- 	ACTION 4 -- Introduce a shrimp  -->
                                      <TD WIDTH="50%"><!--A HREF="game.php?games_id=98586&amp;uniq_id=2883274651551711703533&amp;input=a&amp;sel=4"--><IMG SRC="game/reef/images/a4d.jpg" WIDTH="50" HEIGHT="32" ALIGN="LEFT" BORDER="0" /> <B>Action 4:</B></A></B> Introduce a shrimp<BR />(only once per turn)</TD>
                                      <!-- 	ACTION 8 -- Exchange cube for coral tile  -->
                                      <TD WIDTH="50%"><!--A HREF="game.php?games_id=98586&amp;uniq_id=2883274651551711703533&amp;input=a&amp;sel=8"--><IMG SRC="game/reef/images/a8d.jpg" WIDTH="50" HEIGHT="32" ALIGN="LEFT" BORDER="0" /> <B>Action 8:</B></A></B> Exchange a larva cube for a polyp tile of the same colour</TD>
                                    </TR>
                                    <TR>
                                      <!-- 	ACTION 5 -- Move a shrimp  -->
                                      <TD WIDTH="50%"><IMG SRC="game/reef/images/a5d.jpg" WIDTH="50" HEIGHT="32" ALIGN="LEFT" /> <B>Action 5</B>:</B> Move or remove a shrimp</TD>



                                      <!-- 	ACTION 9 -- Tester link  -->
                                      <TD WIDTH="50%">
                                        <!-- a href="rehack.php?act=shrimplocs" -->
                                        <IMG SRC="game/reef/images/a9d.jpg" WIDTH="50" HEIGHT="32" ALIGN="LEFT" /> <B>Action 9<!-- /a -->:</B> Do none of the above
                                      </TD>


                                    </TR>
                                  </TABLE>
                                </TD>
                              </TR>
                              <TR>
                                <TD CLASS="borderred" BGCOLOR="#FFFFBB">
                                  <TABLE WIDTH="100%">
                                    <TR>
                                      <!-- 	ACTION 10 -- Take new cube, corals from Open Sea  -->
                                      <TD><!-- A HREF="relaunch.php" --><IMG SRC="game/reef/images/a10.jpg" WIDTH="50" HEIGHT="32" ALIGN="LEFT" BORDER="0" /> <B>Action 10:</B><!-- /A --></B> Collect a larva cube and polyp tiles from the open sea<BR />(must do once, at end of turn only)</TD>
                                    </TR>
                                  </TABLE>
                                </TD>
                              </TR>
                              <TR>
                                <TD CLASS="borderred" BGCOLOR="#FFFFBB">
                                  <!-- ############################################################
		   <!-- 	ACTION X -- START OVER  		-- START OVER  --			-->
                                  <!-- ############################################################	-->
                                  <P><!--A HREF="game.php?games_id=98586&amp;uniq_id=2883274651551711703533&amp;input=a&amp;rb=1"-->[Start your turn over]</A></P>
                                </TD>
                              </TR>
                            </TABLE>
                          </TD>
                        </TR>
                      </TABLE>
                      <BR />
                    </TD>
                  </TR>




                  <!-- This area holds the game board that both players can view,
                              all the elements here are shared between the players,
                              most likely anyone who acesses this game will see this board display.
                              It probably uses some sort of inner html file that updates on user input.  -->
                  <TR VALIGN="TOP">
                    <TD COLSPAN="5">
                      <TABLE CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
                        <TR>
                          <TD CLASS="border">
                            <TABLE CELLSPACING="0" CELLPADDING="3" WIDTH="100%">
                              <TR ALIGN="CENTER">
                                <TD CLASS="border" BGCOLOR="#0066CC"><SPAN CLASS="yellow_text_bold">Game Board</SPAN></TD>
                              </TR>
                              <TR>
                                <TD CLASS="border">
                                  <TABLE CELLPADDING="0" CELLSPACING="0" WIDTH="100%">
                                    <TR>
                                      <TD ALIGN="CENTER">
                                        <TABLE CELLPADDING="0" CELLSPACING="2">
                                          <!-- This area holds the polyp tile values  -->
                                          <TR ALIGN="CENTER" VALIGN="BOTTOM">
                                            <!-- ############################################################
		   <!-- 	DOMINANCE TILES  		DOMINANCE TILES
		   <!-- ############################################################  -->
                                            <!-- #1  -->
                                            <TD>
                                              &nbsp;<BR />
                                              <TABLE CELLPADDING="0" CELLSPACING="0" STYLE="border: solid 1px black;">
                                                <TR>
                                                  <TD><IMG SRC="game/reef/images/p1.jpg" WIDTH="32" HEIGHT="32" /></TD>
                                                  <TD><IMG SRC="game/reef/images/p1.jpg" WIDTH="32" HEIGHT="32" /></TD>
                                                </TR>
                                                <TR>
                                                  <TD><IMG SRC="game/reef/images/ct01.gif" WIDTH="32" HEIGHT="32" /></TD>
                                                  <TD><IMG SRC="game/reef/images/p0.jpg" WIDTH="32" HEIGHT="32" /></TD>
                                                </TR>
                                              </TABLE>
                                              <B>1</B>
                                            </TD>

                                            <!-- #2  -->
                                            <TD>
                                              &nbsp;<BR />
                                              <TABLE CELLPADDING="0" CELLSPACING="0" STYLE="border: solid 1px black;">
                                                <TR>
                                                  <TD><IMG SRC="game/reef/images/p2.jpg" WIDTH="32" HEIGHT="32" /></TD>
                                                  <TD><IMG SRC="game/reef/images/p2.jpg" WIDTH="32" HEIGHT="32" /></TD>
                                                </TR>
                                                <TR>
                                                  <TD><IMG SRC="game/reef/images/ct11.gif" WIDTH="32" HEIGHT="32" /></TD>
                                                  <TD><IMG SRC="game/reef/images/p0.jpg" WIDTH="32" HEIGHT="32" /></TD>
                                                </TR>
                                              </TABLE>
                                              <B>2</B>
                                            </TD>
                                            <!-- #3  -->
                                            <TD>
                                              &nbsp;<BR />
                                              <TABLE CELLPADDING="0" CELLSPACING="0" STYLE="border: solid 1px black;">
                                                <TR>
                                                  <TD><IMG SRC="game/reef/images/p2.jpg" WIDTH="32" HEIGHT="32" /></TD>
                                                  <TD><IMG SRC="game/reef/images/p2.jpg" WIDTH="32" HEIGHT="32" /></TD>
                                                </TR>
                                                <TR>
                                                  <TD><IMG SRC="game/reef/images/ct21.gif" WIDTH="32" HEIGHT="32" /></TD>
                                                  <TD><IMG SRC="game/reef/images/p1.jpg" WIDTH="32" HEIGHT="32" /></TD>
                                                </TR>
                                              </TABLE>
                                              <B>3</B>
                                            </TD>
                                            <!-- #4  -->
                                            <TD>
                                              &nbsp;<BR />
                                              <TABLE CELLPADDING="0" CELLSPACING="0" STYLE="border: solid 1px black;">
                                                <TR>
                                                  <TD><IMG SRC="game/reef/images/p3.jpg" WIDTH="32" HEIGHT="32" /></TD>
                                                  <TD><IMG SRC="game/reef/images/p3.jpg" WIDTH="32" HEIGHT="32" /></TD>
                                                </TR>
                                                <TR>
                                                  <TD><IMG SRC="game/reef/images/ct31.gif" WIDTH="32" HEIGHT="32" /></TD>
                                                  <TD><IMG SRC="game/reef/images/p1.jpg" WIDTH="32" HEIGHT="32" /></TD>
                                                </TR>
                                              </TABLE>
                                              <B>4</B>
                                            </TD>
                                            <!-- #5  -->
                                            <TD>
                                              &nbsp;<BR />
                                              <TABLE CELLPADDING="0" CELLSPACING="0" STYLE="border: solid 1px black;">
                                                <TR>
                                                  <TD><IMG SRC="game/reef/images/p3.jpg" WIDTH="32" HEIGHT="32" /></TD>
                                                  <TD><IMG SRC="game/reef/images/p3.jpg" WIDTH="32" HEIGHT="32" /></TD>
                                                </TR>
                                                <TR>
                                                  <TD><IMG SRC="game/reef/images/ct41.gif" WIDTH="32" HEIGHT="32" /></TD>
                                                  <TD><IMG SRC="game/reef/images/p2.jpg" WIDTH="32" HEIGHT="32" /></TD>
                                                </TR>
                                              </TABLE>
                                              <B>5</B>
                                            </TD>
                                            <!-- #6  -->
                                            <TD>
                                              &nbsp;<BR />
                                              <TABLE CELLPADDING="0" CELLSPACING="0" STYLE="border: solid 1px black;">
                                                <TR>
                                                  <TD><IMG SRC="game/reef/images/p4.jpg" WIDTH="32" HEIGHT="32" /></TD>
                                                  <TD><IMG SRC="game/reef/images/p4.jpg" WIDTH="32" HEIGHT="32" /></TD>
                                                </TR>
                                                <TR>
                                                  <TD><IMG SRC="game/reef/images/ct51.gif" WIDTH="32" HEIGHT="32" /></TD>
                                                  <TD><IMG SRC="game/reef/images/p2.jpg" WIDTH="32" HEIGHT="32" /></TD>
                                                </TR>
                                              </TABLE>
                                              <B>6</B>
                                            </TD>
                                            <!-- #7  -->
                                            <TD>
                                              &nbsp;<BR />
                                              <TABLE CELLPADDING="0" CELLSPACING="0" STYLE="border: solid 1px black;">
                                                <TR>
                                                  <TD><IMG SRC="game/reef/images/p0.jpg" WIDTH="32" HEIGHT="32" /></TD>
                                                  <TD><IMG SRC="game/reef/images/p0.jpg" WIDTH="32" HEIGHT="32" /></TD>
                                                </TR>
                                                <TR>
                                                  <TD><IMG SRC="game/reef/images/ct61.gif" WIDTH="32" HEIGHT="32" /></TD>
                                                  <TD><IMG SRC="game/reef/images/p3.jpg" WIDTH="32" HEIGHT="32" /></TD>
                                                </TR>
                                              </TABLE>
                                              <B>7</B>
                                            </TD>
                                            <!-- #8  -->
                                            <TD>
                                              &nbsp;<BR />
                                              <TABLE CELLPADDING="0" CELLSPACING="0" STYLE="border: solid 1px black;">
                                                <TR>
                                                  <TD><IMG SRC="game/reef/images/p4.jpg" WIDTH="32" HEIGHT="32" /></TD>
                                                  <TD><IMG SRC="game/reef/images/p4.jpg" WIDTH="32" HEIGHT="32" /></TD>
                                                </TR>
                                                <TR>
                                                  <TD><IMG SRC="game/reef/images/ct71.gif" WIDTH="32" HEIGHT="32" /></TD>
                                                  <TD><IMG SRC="game/reef/images/p3.jpg" WIDTH="32" HEIGHT="32" /></TD>
                                                </TR>
                                              </TABLE>
                                              <B>8</B>
                                            </TD>
                                            <!-- #9  -->
                                            <TD>
                                              &nbsp;<BR />
                                              <TABLE CELLPADDING="0" CELLSPACING="0" STYLE="border: solid 1px black;">
                                                <TR>
                                                  <TD><IMG SRC="game/reef/images/p0.jpg" WIDTH="32" HEIGHT="32" /></TD>
                                                  <TD><IMG SRC="game/reef/images/p0.jpg" WIDTH="32" HEIGHT="32" /></TD>
                                                </TR>
                                                <TR>
                                                  <TD><IMG SRC="game/reef/images/ct81.gif" WIDTH="32" HEIGHT="32" /></TD>
                                                  <TD><IMG SRC="game/reef/images/p4.jpg" WIDTH="32" HEIGHT="32" /></TD>
                                                </TR>
                                              </TABLE>
                                              <B>9</B>
                                            </TD>
                                            <!-- #10  -->
                                            <TD>
                                              &nbsp;<BR />
                                              <TABLE CELLPADDING="0" CELLSPACING="0" STYLE="border: solid 1px black;">
                                                <TR>
                                                  <TD><IMG SRC="game/reef/images/p1.jpg" WIDTH="32" HEIGHT="32" /></TD>
                                                  <TD><IMG SRC="game/reef/images/p1.jpg" WIDTH="32" HEIGHT="32" /></TD>
                                                </TR>
                                                <TR>
                                                  <TD><IMG SRC="game/reef/images/ct91.gif" WIDTH="32" HEIGHT="32" /></TD>
                                                  <TD><IMG SRC="game/reef/images/p4.jpg" WIDTH="32" HEIGHT="32" /></TD>
                                                </TR>
                                              </TABLE>
                                              <B>10</B>
                                            </TD>
                                          </TR>
                                        </TABLE>
                                        <!-- ###########################################  -->
                                        <!-- 		OPEN SEA  		OPEN SEA 			 -->
                                        <!-- ###########################################  -->
                                        <P>
                                        <TABLE CELLPADDING="2" CELLSPACING="2" BORDER="0">
                                          <TR ALIGN="CENTER" VALIGN="TOP">
                                            <TD WIDTH="115" BGCOLOR="#6DC0B4" STYLE="border: solid 1px black;">
                                              <TABLE CELLSPACING="2" CELLPADDING="0">
                                                <TR ALIGN="CENTER">
                                                  <TD><IMG BORDER="1" SRC="game/reef/images/l0.gif" ALT="[w]" WIDTH="16" HEIGHT="16" ALIGN="ABSMIDDLE" /><BR />+8 in supply</TD>
                                                </TR>
                                                <TR>
                                                  <TD></TD>
                                                </TR>
                                                <TR ALIGN="CENTER">
                                                  <TD><IMG STYLE="border: solid 1px #4F4F4F;" SRC="game/reef/images/p2.jpg" HEIGHT="32" WIDTH="32" ALT="o" /> <IMG STYLE="border: solid 1px #4F4F4F;" SRC="game/reef/images/p0.jpg" HEIGHT="32" WIDTH="32" ALT="w" /></TD>
                                                </TR>
                                              </TABLE>
                                            </TD>
                                            <TD WIDTH="115" BGCOLOR="#6DC0B4" STYLE="border: solid 1px black;">
                                              <TABLE CELLSPACING="2" CELLPADDING="0">
                                                <TR ALIGN="CENTER">
                                                  <TD><IMG BORDER="1" SRC="game/reef/images/l1.gif" ALT="[y]" WIDTH="16" HEIGHT="16" ALIGN="ABSMIDDLE" /><BR />+9 in supply</TD>
                                                </TR>
                                                <TR>
                                                  <TD></TD>
                                                </TR>
                                                <TR ALIGN="CENTER">
                                                  <TD><IMG STYLE="border: solid 1px #4F4F4F;" SRC="game/reef/images/p1.jpg" HEIGHT="32" WIDTH="32" ALT="y" /></TD>
                                                </TR>
                                              </TABLE>
                                            </TD>
                                            <TD WIDTH="115" BGCOLOR="#6DC0B4" STYLE="border: solid 1px black;">
                                              <TABLE CELLSPACING="2" CELLPADDING="0">
                                                <TR ALIGN="CENTER">
                                                  <TD><IMG BORDER="1" SRC="game/reef/images/l2.gif" ALT="[o]" WIDTH="16" HEIGHT="16" ALIGN="ABSMIDDLE" /><BR />+9 in supply</TD>
                                                </TR>
                                                <TR>
                                                  <TD></TD>
                                                </TR>
                                                <TR ALIGN="CENTER">
                                                  <TD><IMG STYLE="border: solid 1px #4F4F4F;" SRC="game/reef/images/p2.jpg" HEIGHT="32" WIDTH="32" ALT="o" /> <IMG STYLE="border: solid 1px #4F4F4F;" SRC="game/reef/images/p3.jpg" HEIGHT="32" WIDTH="32" ALT="p" /> <IMG STYLE="border: solid 1px #4F4F4F;" SRC="game/reef/images/p4.jpg" HEIGHT="32" WIDTH="32" ALT="g" /></TD>
                                                </TR>
                                              </TABLE>
                                            </TD>
                                            <TD WIDTH="115" BGCOLOR="#6DC0B4" STYLE="border: solid 1px black;">
                                              <TABLE CELLSPACING="2" CELLPADDING="0">
                                                <TR ALIGN="CENTER">
                                                  <TD><IMG BORDER="1" SRC="game/reef/images/l3.gif" ALT="[p]" WIDTH="16" HEIGHT="16" ALIGN="ABSMIDDLE" /><BR />+7 in supply</TD>
                                                </TR>
                                                <TR>
                                                  <TD></TD>
                                                </TR>
                                                <TR ALIGN="CENTER">
                                                  <TD><IMG STYLE="border: solid 1px #4F4F4F;" SRC="game/reef/images/p2.jpg" HEIGHT="32" WIDTH="32" ALT="o" /> <IMG STYLE="border: solid 1px #4F4F4F;" SRC="game/reef/images/p4.jpg" HEIGHT="32" WIDTH="32" ALT="g" /> <IMG STYLE="border: solid 1px #4F4F4F;" SRC="game/reef/images/p0.jpg" HEIGHT="32" WIDTH="32" ALT="w" /></TD>
                                                </TR>
                                              </TABLE>
                                            </TD>
                                            <TD WIDTH="115" BGCOLOR="#6DC0B4" STYLE="border: solid 1px black;">
                                              <TABLE CELLSPACING="2" CELLPADDING="0">
                                                <TR ALIGN="CENTER">
                                                  <TD><IMG BORDER="1" SRC="game/reef/images/l4.gif" ALT="[g]" WIDTH="16" HEIGHT="16" ALIGN="ABSMIDDLE" /><BR />+8 in supply</TD>
                                                </TR>
                                                <TR>
                                                  <TD></TD>
                                                </TR>
                                                <TR ALIGN="CENTER">
                                                  <TD><IMG STYLE="border: solid 1px #4F4F4F;" SRC="game/reef/images/p0.jpg" HEIGHT="32" WIDTH="32" ALT="w" /> <IMG STYLE="border: solid 1px #4F4F4F;" SRC="game/reef/images/p3.jpg" HEIGHT="32" WIDTH="32" ALT="p" /> <IMG STYLE="border: solid 1px #4F4F4F;" SRC="game/reef/images/p4.jpg" HEIGHT="32" WIDTH="32" ALT="g" /></TD>
                                                </TR>
                                              </TABLE>
                                            </TD>
                                            <TD></TD>
                                            <TD WIDTH="110" VALIGN="TOP" STYLE="border: solid 1px black; font-size: 11px; font-weight: bold; padding-top: 6px;">Alga Cylinder Space<BR /><BR /><BR />none</TD>
                                          </TR>
                                        </TABLE>
                                        <!-- This is the LEFT reef board  -->
                                        <P>
                                        <TABLE CELLPADDING="0" CELLSPACING="5">
                                          <TR VALIGN="TOP">
                                            <TD>
                                              <DIV STYLE="left:0px; top:0px; width:340; height:291; position:relative; border: solid 1px black;">
                                                <SPAN ID="map" STYLE="left:0; top:0; position:absolute; z-index:100;">
                                                  <IMG SRC="game/reef/images/b0.jpg" />
                                                </SPAN>
                                                <!-- NUMBERS -->
                                                <?php
                                                for ($i = 1; $i <= 6; $i++) {
                                                    echo '<SPAN STYLE="left:0; top:' . (($i * 40) + 19) . '; width:18px; position:absolute; z-index:200; font-weight: bold; font-size: 12px; text-align: center;">' . $i . '</SPAN>';
                                                }
                                                ?>
                                                <!-- LETTERS -->
                                                <?php
                                                $letters = array('A', 'B', 'C', 'D', 'E', 'F', 'G');
                                                for ($i = 0; $i < 7; $i++) {
                                                    echo '<SPAN STYLE="left:' . (($i * 40) + 44) . '; top:4; width:0px; height: 0px; position:absolute; z-index:200; font-weight: bold; font-size: 12px;">' . $letters[$i] . '</SPAN>';
                                                }
                                                ?>
                                                <!-- INITIAL TILES -->
                                                <?php
                                                $tiles = array(
                                                    2 => array('left' => 152, 'top' => 67, 'img' => 'p1.jpg'),
                                                    3 => array('left' => 192, 'top' => 107, 'img' => 'p0.jpg'),
                                                    4 => array('left' => 72, 'top' => 147, 'img' => 'p4.jpg'),
                                                    5 => array('left' => 112, 'top' => 187, 'img' => 'p3.jpg'),
                                                    5 => array('left' => 192, 'top' => 187, 'img' => 'p2.jpg')
                                                );
                                                foreach ($tiles as $tile) {
                                                    echo '<SPAN STYLE="left:' . $tile['left'] . '; top:' . $tile['top'] . '; width:32px; height:32px; position:absolute; z-index:200; border: solid 2px #0000AF;">';
                                                    echo '<IMG SRC="game/reef/images/' . $tile['img'] . '" HEIGHT="32" WIDTH="32" />';
                                                    echo '</SPAN>';
                                                }
                                                ?>
                                                <!-- CLICKABLE AREAS FOR SHRIMP PLACEMENT -->
                                                <?php
                                                for ($i = 0; $i < 42; $i++) {
                                                    if (isValidShrimpPosition($bord, $i)) {
                                                        $top = cel2top($i);
                                                        $left = cel2left($i);
                                                        echo '<A HREF="' . kFNAME . '.php?place_shrimp=1&cell=' . $i . '" STYLE="left:' . $left . 'px; top:' . $top . 'px; width:32px; height:32px; position:absolute; z-index:300; border: solid 2px #00FF00; display:block;"></A>';
                                                    }
                                                }
                                                ?>
                                              </DIV>
                                            </TD>
                                            <!-- This is the RIGHT board  -->
                                            <TD>
                                              <DIV STYLE="left:0px; top:0px; width:340; height:291; position:relative; border: solid 1px black;">
                                                <SPAN ID="map" STYLE="left:0; top:0; position:absolute; z-index:100;">
                                                  <IMG SRC="game/reef/images/b3.jpg" />
                                                </SPAN>
                                                <!-- NUMBERS -->
                                                <?php
                                                for ($i = 1; $i <= 6; $i++) {
                                                    echo '<SPAN STYLE="left:320; top:' . (($i * 40) + 19) . '; width:18px; position:absolute; z-index:200; font-weight: bold; font-size: 12px; text-align: center;">' . $i . '</SPAN>';
                                                }
                                                ?>
                                                <!-- LETTERS -->
                                                <?php
                                                $letters = array('H', 'I', 'J', 'K', 'L', 'M', 'N');
                                                for ($i = 0; $i < 7; $i++) {
                                                    echo '<SPAN STYLE="left:' . (($i * 40) + 44) . '; top:4; width:0px; height: 0px; position:absolute; z-index:200; font-weight: bold; font-size: 12px;">' . $letters[$i] . '</SPAN>';
                                                }
                                                ?>
                                                <!-- INITIAL TILES -->
                                                <?php
                                                $tiles = array(
                                                    2 => array('left' => 152, 'top' => 67, 'img' => 'p2.jpg'),
                                                    3 => array('left' => 192, 'top' => 107, 'img' => 'p4.jpg'),
                                                    4 => array('left' => 72, 'top' => 147, 'img' => 'p0.jpg'),
                                                    5 => array('left' => 112, 'top' => 187, 'img' => 'p3.jpg'),
                                                    5 => array('left' => 192, 'top' => 187, 'img' => 'p1.jpg')
                                                );
                                                foreach ($tiles as $tile) {
                                                    echo '<SPAN STYLE="left:' . $tile['left'] . '; top:' . $tile['top'] . '; width:32px; height:32px; position:absolute; z-index:200; border: solid 2px #0000AF;">';
                                                    echo '<IMG SRC="game/reef/images/' . $tile['img'] . '" HEIGHT="32" WIDTH="32" />';
                                                    echo '</SPAN>';
                                                }
                                                ?>
                                                <!-- CLICKABLE AREAS FOR SHRIMP PLACEMENT -->
                                                <?php
                                                for ($i = 0; $i < 42; $i++) {
                                                    if (isValidShrimpPosition($bord, $i)) {
                                                        $top = cel2top($i);
                                                        $left = cel2left($i);
                                                        echo '<A HREF="' . kFNAME . '.php?place_shrimp=1&cell=' . $i . '" STYLE="left:' . $left . 'px; top:' . $top . 'px; width:32px; height:32px; position:absolute; z-index:300; border: solid 2px #00FF00; display:block;"></A>';
                                                    }
                                                }
                                                ?>
                                              </DIV>
                                            </TD>
                                          </TR>
                                        </TABLE>
                                      </TD>
                                    </TR>
                                  </TABLE>
                                </TD>
                              </TR>
                            </TABLE>
                          </TD>
                        </TR>
                      </TABLE>
                    </TD>
                  </TR>
                </TABLE>
              </TD>
            </TR>
          </TABLE>
          <BR />
        </TD>
      </TR>
      <!-- Footer thingy  -->
      <TR>
        <TD CLASS="menu">
          <TABLE CELLPADDING="0" CELLSPACING="0" WIDTH="100%">
            <TR VALIGN="TOP">
              <TD CLASS="small_text">Reef Encounter was designed by Richard Breese; published by <A HREF="http://www.whatsyourgame.it/">What's Your Game?</A> and <A HREF="http://www.zmangames.com/products/store_reef.htm">Z-Man Games</A>.<BR />Originally published by R&D Games with original artwork (used here at SpielByWeb) by Juliet Breese;.<br /><br /><B>If you enjoy playing Reef Encounter please support the designer and publisher by buying a copy of the game.</B><BR />It is available in USA from <A HREF="http://www.funagain.com/cgi-bin/funagain/015479?;;SBYW">Funagain Games</A>, and from other retailers around the world.<br /><br />Play-by-Web coding by Mikael Sheikh based on code by <A HREF="http://www.amarriner.com/">Aaron Marriner</A>.</TD>
              <TD ALIGN="RIGHT">
                <TABLE CELLSPACING="0" CELLPADDING="0">
                  <TR>
                    <TD ALIGN="RIGHT" CLASS="small_text">Users Registered:</TD>
                    <TD WIDTH="3"></TD>
                    <TD CLASS="small_text">7821</TD>
                  </TR>
                  <TR>
                    <TD ALIGN="RIGHT" CLASS="small_text">Games Waiting:</TD>
                    <TD WIDTH="3"></TD>
                    <TD CLASS="small_text">136</TD>
                  </TR>
                  <TR>
                    <TD ALIGN="RIGHT" CLASS="small_text">Games in Progress:</TD>
                    <TD WIDTH="3"></TD>
                    <TD CLASS="small_text">378</TD>
                  </TR>
                  <TR>
                    <TD ALIGN="RIGHT" CLASS="small_text">Completed Games:</TD>
                    <TD WIDTH="3"></TD>
                    <TD CLASS="small_text">88257</TD>
                  </TR>
                </TABLE>
              </TD>
            </TR>
          </TABLE>
        </TD>
      </TR>
    </TABLE>
  </DIV>
  <script type="text/javascript">
    var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
    document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
  </script>
  <script type="text/javascript">
    var pageTracker = _gat._getTracker("UA-3991266-1");
    pageTracker._initData();
    pageTracker._trackPageview();
  </script>
</BODY>

</HTML>
