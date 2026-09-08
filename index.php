<?php
//Script Author: phccoder https://t.me/PHCC0D3r
include 'init.php';

if ($forceAuth && !isset($_SESSION["Auth"])) {
    header("location: ./auth.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
	<meta charset="utf-8">
	<title><?php echo $site_name;?></title>
	<meta name="author" content="<?php echo $owner ?>">
	<link rel="icon" href="<?php echo $site_icon; ?>" type="image/png">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="<?php echo $owner ?> CC Checker">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="./assets/css/style.css"/>
</head>
<body>
	<div class="bg-gradient"></div>

	<div class="app-container">
		<main class="glass card-main">

			<!-- Header -->
			<header class="card-header">
				<img class="avatar" src="<?php echo $site_icon; ?>" alt="logo">
				<h1>SK / CC Checker</h1>
				<p>Stripe Key &amp; Card Validator</p>
			</header>

			<!-- Card input -->
			<section class="glass-section">
				<label class="field-label" for="cards">Generated Cards</label>
				<textarea class="glass-input" id="cards" rows="5" placeholder="xxxxxxxxxxxxxxxx|xx|xxxx|xxx" required></textarea>
			</section>

			<!-- API + SK -->
			<section class="glass-section">
				<label class="field-label" for="api">API</label>
				<div class="input-group">
					<select class="glass-input" id="api" onchange="showDiv('sk_section', this)">
						<?php for ($i = 0; $i < count($api_file); $i++) {
							echo '<option value="'.$api_file[$i].'">'.$api_name[$i].'</option>';
						}?>
					</select>
					<div class="input-actions">
						<button class="btn btn-glass btn-icon" type="button" onclick="Settings();" title="Settings" aria-label="Settings">
							<svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8.837 1.626c-.246-.835-1.428-.835-1.674 0l-.094.319A1.873 1.873 0 0 1 4.377 3.06l-.292-.16c-.764-.415-1.6.42-1.184 1.185l.159.292a1.873 1.873 0 0 1-1.115 2.692l-.319.094c-.835.246-.835 1.428 0 1.674l.319.094a1.873 1.873 0 0 1 1.115 2.693l-.16.291c-.415.764.42 1.6 1.185 1.184l.292-.159a1.873 1.873 0 0 1 2.692 1.116l.094.318c.246.835 1.428.835 1.674 0l.094-.319a1.873 1.873 0 0 1 2.693-1.115l.291.16c.764.415 1.6-.42 1.184-1.185l-.159-.291a1.873 1.873 0 0 1 1.116-2.693l.318-.094c.835-.246.835-1.428 0-1.674l-.319-.094a1.873 1.873 0 0 1-1.115-2.692l.16-.292c.415-.764-.42-1.6-1.185-1.184l-.291.159A1.873 1.873 0 0 1 8.93 1.945l-.094-.319zm-2.633-.283c.527-1.79 3.065-1.79 3.592 0l.094.319a.873.873 0 0 0 1.255.52l.292-.16c1.64-.892 3.434.901 2.54 2.541l-.159.292a.873.873 0 0 0 .52 1.255l.319.094c1.79.527 1.79 3.065 0 3.592l-.319.094a.873.873 0 0 0-.52 1.255l.16.292c.893 1.64-.902 3.434-2.541 2.54l-.292-.159a.873.873 0 0 0-1.255.52l-.094.319c-.527 1.79-3.065 1.79-3.592 0l-.094-.319a.873.873 0 0 0-1.255-.52l-.292.16c-1.64.893-3.433-.902-2.54-2.541l.159-.292a.873.873 0 0 0-.52-1.255l-.319-.094c-1.79-.527-1.79-3.065 0-3.592l.319-.094a.873.873 0 0 0 .52-1.255l-.16-.292c-.892-1.64.902-3.433 2.541-2.54l.292.159a.873.873 0 0 0 1.255-.52l.094-.319z"/><path fill-rule="evenodd" d="M8 5.754a2.246 2.246 0 1 0 0 4.492 2.246 2.246 0 0 0 0-4.492zM4.754 8a3.246 3.246 0 1 1 6.492 0 3.246 3.246 0 0 1-6.492 0z"/></svg>
						</button>
						<button class="btn btn-glass" type="button" onclick="modalCCGEN();">CC Gen</button>
					</div>
				</div>

				<div id="sk_section" class="hidden" style="margin-top: 12px;">
					<label class="field-label" for="sk">Stripe Secret Key (SK)</label>
					<div class="input-group">
						<input type="text" class="glass-input" id="sk" placeholder="sk_live_xxxxxxxxxxxxxxxxxx">
						<div class="input-actions">
							<button class="btn btn-glass btn-icon" type="button" onclick="copySK();" title="Copy SK" aria-label="Copy SK">
								<svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/><path fill-rule="evenodd" d="M9.5 1h-3a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z"/></svg>
							</button>
							<button class="btn btn-glass" type="button" onclick="checkSK();">CHECK SK</button>
						</div>
					</div>
				</div>

				<div style="margin-top: 16px;">
					<button type="button" class="btn btn-accent btn-block" onclick="checkCards();">CHECK CARDS</button>
				</div>
			</section>

			<!-- Stats bar -->
			<section class="stats-bar">
				<div class="stat-pill stat-cvv" data-filter="cvv" id="cardsLiveCVV">
					<span class="stat-label">CVV</span>
					<span class="stat-count" id="approved_counter_cvv">0</span>
					<button type="button" class="btn btn-glass btn-sm stat-clear" onclick="clearCVV();">Clear</button>
				</div>
				<div class="stat-pill stat-ccn" data-filter="ccn" id="cardsLiveCCN">
					<span class="stat-label">CCN</span>
					<span class="stat-count" id="approved_counter_ccn">0</span>
					<button type="button" class="btn btn-glass btn-sm stat-clear" onclick="clearCCN();">Clear</button>
				</div>
				<div class="stat-pill stat-dead" data-filter="dead" id="cardsDead">
					<span class="stat-label">DEAD</span>
					<span class="stat-count" id="decline_counter">0</span>
					<button type="button" class="btn btn-glass btn-sm stat-clear" onclick="clearDead();">Clear</button>
				</div>
			</section>

			<!-- Results -->
			<section class="glass-section">
				<span class="section-label">Results</span>
				<div class="results-header">
					<div class="run-status">
						<span class="run-dot idle" id="runDot"></span>
						<span id="runStatus">Idle</span>
						<span id="runPending" class="run-pending"></span>
					</div>
					<div class="results-actions">
						<button type="button" class="btn btn-glass btn-sm" id="pauseBtn" disabled>Pause</button>
						<button type="button" class="btn btn-glass btn-sm" id="sortBtn">▲ Newest</button>
						<button type="button" class="btn btn-glass btn-sm" id="clearAllBtn">Clear all</button>
					</div>
				</div>
				<div class="results-container" id="results"></div>
			</section>

			<!-- Footer -->
			<footer class="card-footer">Designed by <a href="https://t.me/PHCC0D3r" target="_blank" rel="noopener">PHC-Coder</a></footer>
		</main>
	</div>

	<!-- Theme toggle -->
	<button class="theme-toggle" onclick="toggleTheme();" id="themeButton" aria-label="Toggle theme"><span id="themeIcon">☀</span></button>

	<!-- ===== CC GEN MODAL ===== -->
	<div class="modal-backdrop-custom" id="ccGEN">
		<div class="modal-glass" role="dialog">
			<button type="button" class="modal-close" onclick="closeModalEl('#ccGEN')" aria-label="Close">&times;</button>
			<div class="modal-title">
				<img class="avatar" src="<?php echo $site_icon; ?>" alt="logo">
				<div>CC Generator</div>
			</div>
			<form name="console" id="console" role="form" method="POST">
				<div class="input-group" style="margin-bottom: 12px;">
					<div style="flex:1;">
						<label class="field-label" for="ccpN">BIN</label>
						<input id="ccpN" name="ccp" maxlength="19" type="text" class="glass-input" placeholder="xxxx xxxx xxxx xxxx">
					</div>
					<div style="flex:1;">
						<label class="field-label" for="eccv">CVV</label>
						<input type="text" id="eccv" name="eccv" class="glass-input" placeholder="rnd" value="rnd">
					</div>
				</div>
				<select type="text" name="ccoudatfmt" style="display:none;">
					<option value="CHECKER" selected>CHK</option>
					<option value="CSV">CSV</option>
					<option value="XML">XML</option>
					<option value="JSON">JSON</option>
				</select>
				<input type="hidden" name="tr" value="2000">
				<input type="hidden" name="L" value="1L">
				<div id="bininfo" align="center"></div>
				<div class="input-group" style="flex-wrap: wrap;">
					<div style="flex:1; min-width:40%;">
						<label class="field-label" for="emeses">MONTH</label>
						<select type="text" class="glass-input" name="emeses" id="emeses">
							<option value="rnd">RANDOM</option>
							<option value="01">01 - JAN</option><option value="02">02 - FEB</option>
							<option value="03">03 - MAR</option><option value="04">04 - APR</option>
							<option value="05">05 - MAY</option><option value="06">06 - JUN</option>
							<option value="07">07 - JUL</option><option value="08">08 - AUG</option>
							<option value="09">09 - SEP</option><option value="10">10 - OCT</option>
							<option value="11">11 - NOV</option><option value="12">12 - DEC</option>
						</select>
					</div>
					<div style="flex:1; min-width:40%;">
						<label class="field-label" for="eyear">YEAR</label>
						<select type="text" class="glass-input" name="eyear" id="eyear">
							<option value="rnd">RANDOM</option>
							<option value="2026">2026</option><option value="2027">2027</option>
							<option value="2028">2028</option><option value="2029">2029</option>
							<option value="2030">2030</option><option value="2031">2031</option>
							<option value="2032">2032</option><option value="2033">2033</option>
							<option value="2034">2034</option><option value="2035">2035</option>
						</select>
					</div>
					<div style="flex:1; min-width:40%;">
						<label class="field-label" for="ccghm">QUANTITY</label>
						<input type="number" name="ccghm" id="ccghm" maxlength="4" class="glass-input" value="10">
						<select type="text" name="ccnsp" style="display:none;"><option selected>None</option></select>
					</div>
				</div>
				<div style="margin-top: 16px;">
					<button type="button" class="btn btn-accent btn-block" name="gerar" id="gerar" onclick="playClick();">GENERATE</button>
				</div>
			</form>
		</div>
	</div>

	<!-- ===== TEMPLATE MODAL ===== -->
	<div class="modal-backdrop-custom" id="Modal">
		<div class="modal-glass" role="dialog">
			<button type="button" class="modal-close" onclick="closeModalEl('#Modal')" aria-label="Close">&times;</button>
			<div class="modal-title">
				<img class="avatar" src="<?php echo $site_icon; ?>" alt="logo">
				<div id="ModalTitle"></div>
			</div>
			<p id="ModalMsg" style="text-align:center;color:var(--text-secondary);"></p>
		</div>
	</div>

	<!-- ===== SETTINGS MODAL ===== -->
	<div class="modal-backdrop-custom" id="settingsModal">
		<div class="modal-glass" role="dialog">
			<button type="button" class="modal-close" onclick="closeModalEl('#settingsModal')" aria-label="Close">&times;</button>
			<div class="modal-title">
				<img class="avatar" src="<?php echo $site_icon; ?>" alt="logo">
				<div>Additional Settings</div>
			</div>
			<form name="settingForm" id="settingForm" role="form" method="POST">
				<div>
					<label class="field-label" for="telebot">TELEGRAM FORWARDER</label>
					<div class="input-group">
						<input name="telebot" type="text" id="telebot" class="glass-input" placeholder="Chat ID">
						<div class="input-actions">
							<button class="btn btn-glass btn-icon" type="button" onclick="howto();" title="How to use" aria-label="How to use">
								<svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM8.05 9.6c.336 0 .504-.24.554-.627.04-.534.198-.815.847-1.26.673-.475 1.049-1.09 1.049-1.986 0-1.325-.92-2.227-2.262-2.227-1.02 0-1.792.492-2.1 1.29A1.71 1.71 0 0 0 6 5.48c0 .393.203.64.545.64.272 0 .455-.147.564-.51.158-.592.525-.915 1.074-.915.61 0 1.03.446 1.03 1.084 0 .563-.208.885-.822 1.325-.619.433-.926.914-.926 1.64v.111c0 .428.208.745.585.745z"/><path fill-rule="evenodd" d="M10.273 2.513l-.921-.944.715-.698.622.637.89-.011a2.89 2.89 0 0 1 2.924 2.924l-.01.89.636.622a2.89 2.89 0 0 1 0 4.134l-.637.622.011.89a2.89 2.89 0 0 1-2.924 2.924l-.89-.01-.622.636a2.89 2.89 0 0 1-4.134 0l-.622-.637-.89.011a2.89 2.89 0 0 1-2.924-2.924l.01-.89-.636-.622a2.89 2.89 0 0 1 0-4.134l.637-.622-.011-.89a2.89 2.89 0 0 1 2.924-2.924l.89.01.622-.636a2.89 2.89 0 0 1 4.134 0l-.715.698a1.89 1.89 0 0 0-2.704 0l-.92.944-1.32-.016a1.89 1.89 0 0 0-1.911 1.912l.016 1.318-.944.921a1.89 1.89 0 0 0 0 2.704l.944.92-.016 1.32a1.89 1.89 0 0 0 1.912 1.911l1.318-.016.921.944a1.89 1.89 0 0 0 2.704 0l.92-.944 1.32.016a1.89 1.89 0 0 0 1.911-1.912l-.016-1.318.944-.921a1.89 1.89 0 0 0 0-2.704l-.944-.92.016-1.32a1.89 1.89 0 0 0-1.912-1.911l-1.318.016z"/></svg>
							</button>
							<button class="btn btn-glass btn-icon" type="button" onclick="testBot();" title="Test bot" aria-label="Test bot">
								<svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10.354 6.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7 8.793l2.646-2.647a.5.5 0 0 1 .708 0z"/></svg>
							</button>
						</div>
					</div>
				</div>
				<div id="howto">
					<div style="margin-top:12px;padding:12px;background:var(--input-bg);border-radius:10px;">
						<strong style="display:block;margin-bottom:6px;">How To Use:</strong>
						<p style="font-size:0.8rem;color:var(--text-secondary);margin-bottom:4px;">[1] Open our Telegram Bot: <a href="https://t.me/phc_cc_chcker_bot" target="_blank" rel="noopener">@phc_cc_chcker_bot</a>.</p>
						<p style="font-size:0.8rem;color:var(--text-secondary);margin-bottom:4px;">[2] Copy-Paste the Chat ID given by the bot.</p>
						<p style="font-size:0.8rem;color:var(--text-secondary);">[3] Test the forwarder to check if it works.</p>
					</div>
				</div>
				<div class="input-group" style="flex-wrap: wrap; margin-top:12px;">
					<div style="flex:1; min-width:40%;">
						<label class="field-label" for="tele_msg">MESSAGE</label>
						<select type="text" class="glass-input" id="tele_msg">
							<option value="3">CVV &amp; CCN</option>
							<option value="1">CVV Only</option>
							<option value="2">CCN Only</option>
						</select>
					</div>
					<div style="flex:1; min-width:40%;">
						<label class="field-label" for="delay">DELAY</label>
						<select type="text" class="glass-input" id="delay">
							<option value="0">No Delay</option>
							<option value="200">0.2 Sec</option>
							<option value="500">0.5 Sec</option>
							<option value="1000" selected>1 Sec</option>
							<option value="2000">2 Sec</option>
							<option value="3000">3 Sec</option>
						</select>
					</div>
				</div>
			</form>
			<h6 id="TeleMsg" style="text-align:center;color:var(--text-secondary);margin-top:14px;"></h6>
		</div>
	</div>

	<script src="https://code.jquery.com/jquery-3.7.0.min.js" crossorigin="anonymous"></script>
	<script src="./assets/js/main.js"></script>
</body>
</html>
