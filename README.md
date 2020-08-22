# Warhawk profile extractor

Transforms Warhawk profile data (BIN/XML) into HTML.

**Note:** Currently only supports certain files (see checkboxes below).

## How to use

1. Capture packets while navigating through Warhawk player profiles.
2. Export all of the following files from your capture:
   * [x] Stats_BinaryStatsDownload_Submit.jsp*
   * [x] Stats_GetPlayerStats.jsp*
   * [ ] whgamemodestats.jsp*
   * [ ] whmapstats.jsp*
   * [ ] whovertimestats.jsp*
   * [ ] whplayerstats.jsp*
   * [ ] whstats.jsp*
   * [ ] whteamstats.jsp*
   * [ ] whvehiclestats.jsp*
   * [ ] whweaponstats.jsp*
3. Create an /input/ folder and place those files in it.
4. Open index.php in a web browser.
