#include"scmc.h"
#include<stdlib.h>
#include<stdio.h>
#include<string.h>
#include<math.h>
#include<time.h>
#include"expat.h"
int argc;
char **argv;

char str_res[3][50]={
	"ore",
	"gas",
	"ore and gas",
};
char str_ally[3][50]={
	"Enemy",
	"Ally",
	"Allied Victory",
};
char str_order[3][50]={
	"move",
	"patrol",
	"attack",
};
char str_forc[4][50]={
	"[Forc] Force 1",
	"[Forc] Force 2",
	"[Forc] Force 3",
	"[Forc] Force 4",
};
char str_score[8][50]={
	"Total",
	"Units",
	"Buildings",
	"Units And Buildings",
	"Kills",
	"Razings",
	"Kills And Razings",
	"Custom",
};
char str_era[8][50]={
	"Badlands",
	"Space Platform",
	"Installation",
	"Ashworld",
	"Jungle",
	"Desert",
	"Arctic",
	"Twilight",
};
char str_side[8][50]={
	"Zerg",
	"Terran",
	"Protoss",
	"Independent",
	"Neutral",
	"User Selectable",
	"Random",
	"Inactive",
};
char str_ownr[9][50]={
	"Inactive",
	"Computer2",
	"Human2",
	"Rescue",
	"Unused",
	"Computer",
	"Human",
	"Neutral",
	"Closed",
};
char str_mbrf[10][50]={
	"[Mbrf] No Action",
	"[Mbrf] Wait",
	"[Mbrf] Play WAV",
	"[Mbrf] Text Message",
	"[Mbrf] Mission Objectives",
	"[Mbrf] Show Portrait",
	"[Mbrf] Hide Portrait",
	"[Mbrf] Display Speaking Portrait",
	"[Mbrf] Transmission",
	"[Mbrf] Skip Tutorial Enabled",
};
char str_mod[12][50]={
	"At least",
	"At most",
	"is set",
	"not set",
	"set",
	"clear",
	"toggle",
	"Set To",
	"Add",
	"Subtract",
	"Exactly",
	"randomize",
};
char str_colr[18][50]={
	"Red",
	"Blue",
	"Teal",
	"Purple",
	"Orange",
	"Brown",
	"White",
	"Yellow",
	"Green",
	"Pale Yellow",
	"Tan",
	"Neutral Color",
	"Pale Green",
	"Blueish Gray",
	"Pale Yellow 2",
	"Cyan",
	"Unused Color 16",
	"Black",
};
char str_conditions[24][50]={
	"No Condition",
	"Countdown Timer",
	"Command",
	"Bring",
	"Accumulate",
	"Kill",
	"Command the Most",
	"Command the Most At",
	"Most Kills",
	"Highest Score",
	"Most Resources",
	"Switch",
	"Elapsed Time",
	"Data is a Mission Briefing",
	"Opponents",
	"Deaths",
	"Command the Least",
	"Command the Least At",
	"Least Kills",
	"Lowest Score",
	"Least Resources",
	"Score",
	"Always",
	"Never",
};
char str_groups[27][50]={
	"Player 1",
	"Player 2",
	"Player 3",
	"Player 4",
	"Player 5",
	"Player 6",
	"Player 7",
	"Player 8",
	"Player 9",
	"Player 10",
	"Player 11",
	"Player 12",
	"None",
	"Current Player",
	"Foes",
	"Allies",
	"Neutral Players",
	"All players",
	"Force 1",
	"Force 2",
	"Force 3",
	"Force 4",
	"Unknown 1",
	"Unknown 2",
	"Unknown 3",
	"Unknown 4",
	"Non Allied Victory Players",
};
char str_tech[44][50]={
	"[Tech] Stim Packs",
	"[Tech] Lockdown",
	"[Tech] EMP Shockwave",
	"[Tech] Spider Mines",
	"[Tech] Scanner Sweep",
	"[Tech] Tank Siege Mode",
	"[Tech] Defensive Matrix",
	"[Tech] Irradiate",
	"[Tech] Yamato Gun",
	"[Tech] Cloaking Field",
	"[Tech] Personnel Cloaking",
	"[Tech] Burrowing",
	"[Tech] Infestation",
	"[Tech] Spawn Broodlings",
	"[Tech] Dark Swarm",
	"[Tech] Plague",
	"[Tech] Consume",
	"[Tech] Ensnare",
	"[Tech] Parasite",
	"[Tech] Psionic Storm",
	"[Tech] Hallucination",
	"[Tech] Recall",
	"[Tech] Statis Field",
	"[Tech] Archon Warp",
	"[Tech] Restoration",
	"[Tech] Disruption Web",
	"[Tech] Unknown Tech26",
	"[Tech] Mind Control",
	"[Tech] Dark Archon Meld",
	"[Tech] Feedback",
	"[Tech] Optical Flare",
	"[Tech] Maelstrom",
	"[Tech] Lurker Aspect",
	"[Tech] Unknown Tech33",
	"[Tech] Healing",
	"[Tech] Unknown Tech35",
	"[Tech] Unknown Tech36",
	"[Tech] Unknown Tech37",
	"[Tech] Unknown Tech38",
	"[Tech] Unknown Tech39",
	"[Tech] Unknown Tech40",
	"[Tech] Unknown Tech41",
	"[Tech] Unknown Tech42",
	"[Tech] Unknown Tech43",
};
char str_actions[60+1][50]={
	"No Action",
	"Victory",
	"Defeat",
	"Preserve Trigger",
	"Wait",
	"Pause Game",
	"Unpause Game",
	"Transmission",
	"Play WAV",
	"Display Text Message",
	"Center View",
	"Create Unit with Properties",
	"Set Mission Objectives",
	"Set Switch",
	"Set Countdown Timer",
	"Run AI Script",
	"Run AI Script At Location",
	"Leaderboard (Control)",
	"Leaderboard (Control At Location)",
	"Leaderboard (Resources)",
	"Leaderboard (Kills)",
	"Leaderboard (Points)",
	"Kill Unit",
	"Kill Unit At Location",
	"Remove Unit",
	"Remove Unit At Location",
	"Set Resources",
	"Set Score",
	"Minimap Ping",
	"Talking Portrait",
	"Mute Unit Speech",
	"Unmute Unit Speech",
	"Leaderboard Computer Players",
	"Leaderboard Goal (Control)",
	"Leaderboard Goal (Control At Location)",
	"Leaderboard Goal (Resources)",
	"Leaderboard Goal (Kills)",
	"Leaderboard Goal (Points)",
	"Move Location",
	"Move Unit",
	"Leaderboard (Greed)",
	"Set Next Scenario",
	"Set Doodad State",
	"Set Invincibility",
	"Create Unit",
	"Set Deaths",
	"Order",
	"Comment",
	"Give Units to Player",
	"Modify Unit Hit Points",
	"Modify Unit Energy",
	"Modify Unit Shield Points",
	"Modify Unit Resource Amount",
	"Modify Unit Hangar Count",
	"Pause Timer",
	"Unpause Timer",
	"Draw",
	"Set Alliance Status",
	"Disable Debug Mode",
	"Enable Debug Mode",
	"Display Text Message Always",
};
char str_upg[61][50]={
	"Terran Infantry Armor",
	"Terran Vehicle Plating",
	"Terran Ship Plating",
	"Zerg Carapace",
	"Zerg Flyer Carapace",
	"Protoss Armor",
	"Protoss Plating",
	"Terran Infantry Weapons",
	"Terran Vehicle Weapons",
	"Terran Ship Weapons",
	"Zerg Melee Attacks",
	"Zerg Missile Attacks",
	"Zerg Flyer Attacks",
	"Protoss Ground Weapons",
	"Protoss Air Weapons",
	"Protoss Plasma Shields",
	"U-238 Shells",
	"Ion Thrusters",
	"Burst Lasers",
	"Titan Reactor",
	"Ocular Implants",
	"Moebius Reactor",
	"Apollo Reactor",
	"Colossus Reactor",
	"Ventral Sacs",
	"Antennae",
	"Pneumatized Carapace",
	"Metabolic Boost",
	"Adrenal Glands",
	"Muscular Augments",
	"Grooved Spines",
	"Gamete Meiosis",
	"Metasynaptic Node",
	"Singularity Charge",
	"Leg Enhancements",
	"Scarab Damage",
	"Reaver Capacity",
	"Gravitic Drive",
	"Sensor Array",
	"Gravitic Boosters",
	"Khaydarin Amulet",
	"Apial Sensors",
	"Gravitic Thrusters",
	"Carrier Capacity",
	"Khaydarin Core",
	"Unknown Upgrade45",
	"Unknown Upgrade46",
	"Argus Jewel",
	"Unknown Upgrade48",
	"Argus Talisman",
	"Unknown Upgrade50",
	"Caduceus Reactor",
	"Chitinous Plating",
	"Anabolic Synthesis",
	"Charon Booster",
	"Unknown Upgrade55",
	"Unknown Upgrade56",
	"Unknown Upgrade57",
	"Unknown Upgrade58",
	"Unknown Upgrade59",
	"Unknown Upgrade60",
};
char str_weap[130][50]={
	"Gauss Rifle",
	"Gauss Rifle (Jim Raynor)",
	"C-10 Concussion Rifle",
	"C-10 Concussion Rifle (Sarah Kerrigan)",
	"Fragmentation Grenade",
	"Fragmentation Grenade (Jim Raynor)",
	"Spider Mines",
	"Twin Autocannons",
	"Hellfire Missile Pack",
	"Twin Autocannons (Alan Schezar)",
	"Hellfire Missile Pack (Alan Schezar)",
	"Arclite Cannon",
	"Arclite Cannon (Edmund Duke)",
	"Fusion Cutter",
	"Fusion Cutter (Harvest)",
	"Gemini Missiles",
	"Burst Lasers",
	"Gemini Missiles (Tom Kazansky)",
	"Burst Lasers (Tom Kazansky)",
	"ATS Laser Battery",
	"ATA Laser Battery",
	"ATS Laser Battery (Norad II+Mengsk+DuGalle)",
	"ATA Laser Battery (Norad II+Mengsk+DuGalle)",
	"ATS Laser Battery (Hyperion)",
	"ATA Laser Battery (Hyperion)",
	"Flame Thrower",
	"Flame Thrower (Gui Montag)",
	"Arclite Shock Cannon",
	"Arclite Shock Cannon (Edmund Duke)",
	"Longbolt Missiles",
	"Yamato Gun",
	"[Weap] Nuclear Missile",
	"Lockdown",
	"EMP Shockwave",
	"Irradiate",
	"Claws",
	"Claws (Devouring One)",
	"Claws (Infested Kerrigan)",
	"Needle Spines",
	"Needle Spines (Hunter Killer)",
	"Kaiser Blades",
	"Kaiser Blades (Torrasque)",
	"Toxic Spores (Broodling)",
	"Spines",
	"Spines (Harvest)",
	"Acid Spray",
	"Acid Spore",
	"Acid Spore (Kukulza)",
	"Glave Wurm",
	"Glave Wurm (Kukulza)",
	"Venom",
	"Venom (Defiler Hero)",
	"Seeker Spores",
	"Subterranean Tentacle",
	"Suicide (Infested Terran)",
	"Suicide (Scourge)",
	"Parasite",
	"Spawn Broodlings",
	"Ensnare",
	"[Weap] Dark Swarm",
	"Plague",
	"Consume",
	"Particle Beam",
	"Particle Beam (Harvest)",
	"Psi Blades",
	"Psi Blades (Fenix)",
	"Phase Disruptor",
	"Phase Disruptor (Fenix)",
	"Psi Assault",
	"Psi Assault (Tassadar+Aldaris)",
	"Psionic Shockwave",
	"Psionic Shockwave (Tassadar/Zeratul Archon)",
	"Unknown72",
	"Dual Photon Blasters",
	"Anti-matter Missiles",
	"Dual Photon Blasters (Mojo)",
	"Anti-matter Missiles (Mojo)",
	"Phase Disruptor Cannon",
	"Phase Disruptor Cannon (Danimoth)",
	"Pulse Cannon",
	"STS Photon Cannon",
	"STA Photon Cannon",
	"Scarab",
	"Statis Field",
	"Psi Storm",
	"Warp Blades (Zeratul)",
	"Warp Blades (Dark Templar Hero)",
	"Missiles",
	"Laser Battery1",
	"Tormentor Missiles",
	"Bombs",
	"Raider Gun",
	"Laser Battery2",
	"Laser Battery3",
	"Dual Photon Blasters",
	"Rechette Grenade",
	"Twin Autocannons (Floor Trap)",
	"Hellfire Missile Pack (Wall Trap)",
	"Flame Thrower (Wall Trap)",
	"Hellfire Missile Pack (Floor Trap)",
	"Neutron Flare",
	"Disruption Web",
	"Restoration",
	"Halo Rockets",
	"Corrosive Acid",
	"Mind Control",
	"Feedback",
	"Optical Flare",
	"Maelstrom",
	"Subterranean Spines",
	"Gauss Rifle0",
	"Warp Blades",
	"C-10 Concussion Rifle (Samir Duran)",
	"C-10 Concussion Rifle (Infested Duran)",
	"Dual Photon Blasters (Artanis)",
	"Anti-matter Missiles (Artanis)",
	"C-10 Concussion Rifle (Alexei Stukov)",
	"Gauss Rifle1",
	"Gauss Rifle2",
	"Gauss Rifle3",
	"Gauss Rifle4",
	"Gauss Rifle5",
	"Gauss Rifle6",
	"Gauss Rifle7",
	"Gauss Rifle8",
	"Gauss Rifle9",
	"Gauss Rifle10",
	"Gauss Rifle11",
	"Gauss Rifle12",
	"Gauss Rifle13",
};
char str_units[228+5][50]={
   "Terran Marine",
   "Terran Ghost",
   "Terran Vulture",
   "Terran Goliath",
   "Goliath Turret",
   "Terran Siege Tank (Tank Mode)",
   "Tank Turret type 1",
   "Terran SCV",
   "Terran Wraith",
   "Terran Science Vessel",
   "Gui Montag (Firebat)",
   "Terran Dropship",
   "Terran Battlecruiser",
   "Vulture Spider Mine",
   "Nuclear Missile",
   "Terran Civilian",
   "Sarah Kerrigan (Ghost)",
   "Alan Schezar (Goliath)",
   "Alan Turret",
   "Jim Raynor (Vulture)",
   "Jim Raynor (Marine)",
   "Tom Kazansky (Wraith)",
   "Magellan (Science Vessel)",
   "Edmund Duke (Siege Tank)",
   "Duke Turret type 1",
   "Edmund Duke (Siege Mode)",
   "Duke Turret type 2",
   "Arcturus Mengsk (Battlecruiser)",
   "Hyperion (Battlecruiser)",
   "Norad II (Battlecruiser)",
   "Terran Siege Tank (Siege Mode)",
   "Tank Turret type 2",
   "Terran Firebat",
   "Scanner Sweep",
   "Terran Medic",
   "Zerg Larva",
   "Zerg Egg",
   "Zerg Zergling",
   "Zerg Hydralisk",
   "Zerg Ultralisk",
   "Zerg Broodling",
   "Zerg Drone",
   "Zerg Overlord",
   "Zerg Mutalisk",
   "Zerg Guardian",
   "Zerg Queen",
   "Zerg Defiler",
   "Zerg Scourge",
   "Torrasque (Ultralisk)",
   "Matriarch (Queen)",
   "Infested Terran",
   "Infested Kerrigan (Infested Terran)",
   "Unclean One (Defiler)",
   "Hunter Killer (Hydralisk)",
   "Devouring One (Zergling)",
   "Kukulza (Mutalisk)",
   "Kukulza (Guardian)",
   "Yggdrasill (Overlord)",
   "Terran Valkyrie",
   "Cocoon",
   "Protoss Corsair",
   "Protoss Dark Templar",
   "Zerg Devourer",
   "Protoss Dark Archon",
   "Protoss Probe",
   "Protoss Zealot",
   "Protoss Dragoon",
   "Protoss High Templar",
   "Protoss Archon",
   "Protoss Shuttle",
   "Protoss Scout",
   "Protoss Arbiter",
   "Protoss Carrier",
   "Protoss Interceptor",
   "Dark Templar (Hero)",
   "Zeratul (Dark Templar)",
   "Tassadar/Zeratul (Archon)",
   "Fenix (Zealot)",
   "Fenix (Dragoon)",
   "Tassadar (Templar)",
   "Mojo (Scout)",
   "Warbringer (Reaver)",
   "Gantrithor (Carrier)",
   "Protoss Reaver",
   "Protoss Observer",
   "Protoss Scarab",
   "Danimoth (Arbiter)",
   "Aldaris (Templar)",
   "Artanis (Scout)",
   "Rhynadon (Badlands)",
   "Bengalaas (Jungle)",
   "Unused type 1",
   "Unused type 2",
   "Scantid (Desert)",
   "Kakaru (Twilight)",
   "Ragnasaur (Ash World)",
   "Ursadon (Ice World)",
   "Zerg Lurker Egg",
   "Raszagal (Dark Templar)",
   "Samir Duran (Ghost)",
   "Alexei Stukov (Ghost)",
   "Map Revealer",
   "Gerard DuGalle (Ghost)",
   "Zerg Lurker",
   "Infested Duran",
   "Disruption Field",
   "Terran Command Center",
   "Terran Comsat Station",
   "Terran Nuclear Silo",
   "Terran Supply Depot",
   "Terran Refinery",
   "Terran Barracks",
   "Terran Academy",
   "Terran Factory",
   "Terran Starport",
   "Terran Control Tower",
   "Terran Science Facility",
   "Terran Covert Ops",
   "Terran Physics Lab",
   "Unused Terran Bldg type 1",
   "Terran Machine Shop",
   "Unused Terran Bldg type 2",
   "Terran Engineering Bay",
   "Terran Armory",
   "Terran Missile Turret",
   "Terran Bunker",
   "Norad II (Crashed Battlecruiser)",
   "Ion Cannon",
   "Uraj Crystal",
   "Khalis Crystal",
   "Infested Command Center",
   "Zerg Hatchery",
   "Zerg Lair",
   "Zerg Hive",
   "Zerg Nydus Canal",
   "Zerg Hydralisk Den",
   "Zerg Defiler Mound",
   "Zerg Greater Spire",
   "Zerg Queen's Nest",
   "Zerg Evolution Chamber",
   "Zerg Ultralisk Cavern",
   "Zerg Spire",
   "Zerg Spawning Pool",
   "Zerg Creep Colony",
   "Zerg Spore Colony",
   "Unused Zerg Bldg",
   "Zerg Sunken Colony",
   "Zerg Overmind (With Shell)",
   "Zerg Overmind",
   "Zerg Extractor",
   "Mature Crysalis",
   "Zerg Cerebrate",
   "Zerg Cerebrate Daggoth",
   "Unused Zerg Bldg 5",
   "Protoss Nexus",
   "Protoss Robotics Facility",
   "Protoss Pylon",
   "Protoss Assimilator",
   "Protoss Unused type 1",
   "Protoss Observatory",
   "Protoss Gateway",
   "Protoss Unused type 2",
   "Protoss Photon Cannon",
   "Protoss Citadel of Adun",
   "Protoss Cybernetics Core",
   "Protoss Templar Archives",
   "Protoss Forge",
   "Protoss Stargate",
   "Stasis Cell/Prison",
   "Protoss Fleet Beacon",
   "Protoss Arbiter Tribunal",
   "Protoss Robotics Support Bay",
   "Protoss Shield Battery",
   "Khaydarin Crystal Formation",
   "Protoss Temple",
   "Xel'Naga Temple",
   "Mineral Field (Type 1)",
   "Mineral Field (Type 2)",
   "Mineral Field (Type 3)",
   "Cave",
   "Cave-in",
   "Cantina",
   "Mining Platform",
   "Independent Command Center",
   "Independent Starport",
   "Jump Gate",
   "Ruins",
   "Kyadarin Crystal Formation",
   "Vespene Geyser",
   "Warp Gate",
   "Psi Disrupter",
   "Zerg Marker",
   "Terran Marker",
   "Protoss Marker",
   "Zerg Beacon",
   "Terran Beacon",
   "Protoss Beacon",
   "Zerg Flag Beacon",
   "Terran Flag Beacon",
   "Protoss Flag Beacon",
   "Power Generator",
   "Overmind Cocoon",
   "Dark Swarm",
   "Floor Missile Trap",
   "Floor Hatch (UNUSED)",
   "Left Upper Level Door",
   "Right Upper Level Door",
   "Left Pit Door",
   "Right Pit Door",
   "Floor Gun Trap",
   "Left Wall Missile Trap",
   "Left Wall Flame Trap",
   "Right Wall Missile Trap",
   "Right Wall Flame Trap",
   "Start Location",
   "Flag",
   "Young Chrysalis",
   "Psi Emitter",
   "Data Disc",
   "Khaydarin Crystal",
   "Mineral Chunk (Type 1)",
   "Mineral Chunk (Type 2)",
   "Vespene Orb (Protoss Type 1)",
   "Vespene Orb (Protoss Type 2)",
   "Vespene Sac (Zerg Type 1)",
   "Vespene Sac (Zerg Type 2)",
   "Vespene Tank (Terran Type 1)",
   "Vespene Tank (Terran Type 2)",
   "None",
   "Any unit",
   "Men",
   "Buildings",
   "Factories",
};

char str_ct[85][50]={
	"player",
	"unit_id",
	"x",
	"y",
	"xe",
	"ye",
	"upgrade",
	"technology",
	"weapon",
	
	"ownr",
	
	"era",
	
	"dim_x",
	"dim_y",
	
	"side",
	
	"mtxm",
	
	"puni_available",
	"puni_availabledefault",
	"puni_default",
	
	"pupx_max",
	"pupx_init",
	"pupx_maxdefault",
	"pupx_initdefault",
	"pupx_default",
	
	"ptex_available",
	"ptex_researched",
	"ptex_availabledefault",
	"ptex_researcheddefault",
	"ptex_default",
	
	"unix_default",
	"unix_health",
	"unix_shield",
	"unix_armor",
	"unix_time",
	"unix_mineral",
	"unix_gas",
	"unix_name",
	"unix_damage",
	"unix_damagebonus",
	
	"upgx_default",
	"upgx_mineral",
	"upgx_mineralfactor",
	"upgx_gas",
	"upgx_gasfactor",
	"upgx_time",
	"upgx_timefactor",
	
	"tecx_default",
	"tecx_mineral",
	"tecx_gas",
	"tecx_time",
	"tecx_energy",
	
	"health",
	"shield",
	"energy",
	"resource",
	"hangar",
	
	"thg2_id",
	
	"mask",
	
	"mrgn_name",
	
	"sprp_name",
	"sprp_desc",
	
	"forc_player",
	"force",
	"forc_name",
	
	"wav_path",
	"wav_name",
	
	"colr",
	
	"trig_group",
	
	"cond_l",
	"cond_g",
	"cond_n",
	"cond_u",
	"cond_m",
	"cond_c",
	"cond_r",
	"cond_f",
	
	"act_l",
	"act_s",
	"act_w",
	"act_t",
	"act_gf",
	"act_gs",
	"act_u",
	"act_c",
	"act_n",
	"act_f",
};

char str_cc[2][0x20][50]={
	{
		"[00]",
		"[01]",
		"[02]",
		"[03]",
		"[04]",
		"[05]",
		"[06]",
		"[07]",
		"[08]",
		"[09]",
		"[0A]",
		"[0B]",
		"[0C]",
		"[0D]",
		"[0E]",
		"[0F]",
		"[10]",
		"[11]",
		"[12]",
		"[13]",
		"[14]",
		"[15]",
		"[16]",
		"[17]",
		"[18]",
		"[19]",
		"[1A]",
		"[1B]",
		"[1C]",
		"[1D]",
		"[1E]",
		"[1F]",
	},
	{
		"",
		"[1]",
		"[2]",
		"[3]",
		"[4]",
		"[5]",
		"[6]",
		"[7]",
		"[8]",
		"",
		"",
		"",
		"",
		"",
		"",
		"",
		"",
		"",
		"[R]",
		"[C]",
		"",
		"",
		"",
		"",
		"",
		"",
		"",
		"",
		"",
		"",
		"",
		"",
	},
};

enum ct{
	ct_player=0,
	ct_unit_id=1,
	ct_x=2,
	ct_y=3,
	ct_xe=4,
	ct_ye=5,
	ct_upgrade=6,
	ct_technology=7,
	ct_weapon=8,

	ct_ownr=9,

	ct_era=10,
	
	ct_dim_x=11,
	ct_dim_y=12,
	
	ct_side=13,

	ct_mtxm=14,

	ct_puni_available=15,
	ct_puni_availabledefault=16,
	ct_puni_default=17,

	ct_pupx_max=18,
	ct_pupx_init=19,
	ct_pupx_maxdefault=20,
	ct_pupx_initdefault=21,
	ct_pupx_default=22,

	ct_ptex_available=23,
	ct_ptex_researched=24,
	ct_ptex_availabledefault=25,
	ct_ptex_researcheddefault=26,
	ct_ptex_default=27,

	ct_unix_default=28,
	ct_unix_health=29,
	ct_unix_shield=30,
	ct_unix_armor=31,
	ct_unix_time=32,
	ct_unix_mineral=33,
	ct_unix_gas=34,
	ct_unix_name=35,
	ct_unix_damage=36,
	ct_unix_damagebonus=37,

	ct_upgx_default=38,
	ct_upgx_mineral=39,
	ct_upgx_mineralfactor=40,
	ct_upgx_gas=41,
	ct_upgx_gasfactor=42,
	ct_upgx_time=43,
	ct_upgx_timefactor=44,

	ct_tecx_default=45,
	ct_tecx_mineral=46,
	ct_tecx_gas=47,
	ct_tecx_time=48,
	ct_tecx_energy=49,

	ct_health=50,
	ct_shield=51,
	ct_energy=52,
	ct_resource=53,
	ct_hangar=54,

	ct_thg2_id=55,

	ct_mask=56,

	ct_mrgn_name=57,

	ct_sprp_name=58,
	ct_sprp_desc=59,

	ct_forc_player=60,
	ct_force=61,
	ct_forc_name=62,

	ct_wav_path=63,
	ct_wav_name=64,

	ct_colr=65,

	ct_trig_group=66,

	ct_cond_l=67,
	ct_cond_g=68,
	ct_cond_n=69,
	ct_cond_u=70,
	ct_cond_m=71,
	ct_cond_c=72,
	ct_cond_r=73,
	ct_cond_f=74,

	ct_act_l=75,
	ct_act_s=76,
	ct_act_w=77,
	ct_act_t=78,
	ct_act_gf=79,
	ct_act_gs=80,
	ct_act_u=81,
	ct_act_c=82,
	ct_act_n=83,
	ct_act_f=84,
};

int cts[85];
int ct;

int data;

int unit_flags;
int thg2_flags;
int mrgn_flags;

int trig_groups;

char wav_path[100];
char wav_name[100];

int16_t
mint_str(char *s){
	int len=strlen(s);
	int j=0;
	while(s[j]){
		if(s[j]=='\\'){
			j++;
			if(s[j]=='\\'){
				len-=1;
				memmove(&s[j],&s[j+1],len-j+1);
			/*
			}else if(s[j]=='['){
				len-=1;
				s[j-1]='[';
				memmove(&s[j],&s[j+1],len-j+1);
			*/
			}else if(s[j]=='x'){
				j++;
				char n[4];
				memcpy(n,&s[j],3);
				n[3]=0;
				char c=strtoll(n,NULL,16);
				j-=2;
				s[j]=c;
				len-=4;
				memmove(&s[j+1],&s[j+5],len-j);
			}else if(s[j]=='r'){
				s[j-1]='\r';
				len-=1;
				memmove(&s[j],&s[j+1],len-j+1);
			}else if(s[j]=='n'){
				s[j-1]='\n';
				len-=1;
				memmove(&s[j],&s[j+1],len-j+1);
			}
		}else{
		/*
			for(int k=0;k<2;k++){
				for(int l=0;l<0x20;l++){
					if(!strncmp(&s[j],str_cc[k][l],4-k)){
						s[j]=l;
						memmove(&s[j+1],&s[j+1+4-1-k],len-j-4+k+1);
						len-=4-k-1;
						goto mintStr0;
					}
				}
			}
mintStr0:
		*/
			j++;
		}
	}
	return(str(s));
}

void XMLCALL
startElement(void *userData,const XML_Char *name,const XML_Char **attr){
	for(int i=0;i<85;i++){
		if(!strcmp(name,str_ct[i])){
			ct=i;
			cts[ct]=0;
			data=1;
			break;
		}
	}
}
void XMLCALL
endElement(void *userData,const XML_Char *name){
	if(!strcmp(name,"ownr")){
		(*ownrb)[cts[ct_player]]=cts[ct_ownr];
	}else if(!strcmp(name,"era")){
		*erab=cts[ct_era];
	}else if(!strcmp(name,"dim_x")){
		(*dimb)[0]=cts[ct_dim_x];
	}else if(!strcmp(name,"dim_y")){
		(*dimb)[1]=cts[ct_dim_y];
	}else if(!strcmp(name,"side")){
		(*sideb)[cts[ct_player]]=cts[ct_side];
	}else if(!strcmp(name,"mtxm")){
		mtxmb[cts[ct_y]*(*dimb)[0]+cts[ct_x]]=cts[ct_mtxm];
	}else if(!strcmp(name,"puni_available")){
		(*punib).a[cts[ct_player]][cts[ct_unit_id]]=cts[ct_puni_available];
	}else if(!strcmp(name,"puni_availabledefault")){
		(*punib).ad[cts[ct_unit_id]]=cts[ct_puni_availabledefault];
	}else if(!strcmp(name,"puni_default")){
		(*punib).d[cts[ct_player]][cts[ct_unit_id]]=cts[ct_puni_default];
	}else if(!strcmp(name,"pupx_max")){
		(*pupxb).m[cts[ct_player]][cts[ct_upgrade]]=cts[ct_pupx_max];
	}else if(!strcmp(name,"pupx_init")){
		(*pupxb).b[cts[ct_player]][cts[ct_upgrade]]=cts[ct_pupx_init];
	}else if(!strcmp(name,"pupx_maxdefault")){
		(*pupxb).md[cts[ct_upgrade]]=cts[ct_pupx_maxdefault];
	}else if(!strcmp(name,"pupx_initdefault")){
		(*pupxb).bd[cts[ct_upgrade]]=cts[ct_pupx_initdefault];
	}else if(!strcmp(name,"pupx_default")){
		(*pupxb).d[cts[ct_player]][cts[ct_upgrade]]=cts[ct_pupx_default];
	}else if(!strcmp(name,"ptex_available")){
		(*ptexb).a[cts[ct_player]][cts[ct_technology]]=cts[ct_ptex_available];
	}else if(!strcmp(name,"ptex_researched")){
		(*ptexb).r[cts[ct_player]][cts[ct_technology]]=cts[ct_ptex_researched];
	}else if(!strcmp(name,"ptex_availabledefault")){
		(*ptexb).ad[cts[ct_technology]]=cts[ct_ptex_availabledefault];
	}else if(!strcmp(name,"ptex_researcheddefault")){
		(*ptexb).rd[cts[ct_technology]]=cts[ct_ptex_researcheddefault];
	}else if(!strcmp(name,"ptex_default")){
		(*ptexb).d[cts[ct_player]][cts[ct_technology]]=cts[ct_ptex_default];
	}else if(!strcmp(name,"unix_default")){
		(*unixb).d[cts[ct_unit_id]]=cts[ct_unix_default];
	}else if(!strcmp(name,"unix_health")){
		(*unixb).hp[cts[ct_unit_id]]=cts[ct_unix_health];
	}else if(!strcmp(name,"unix_shield")){
		(*unixb).sp[cts[ct_unit_id]]=cts[ct_unix_shield];
	}else if(!strcmp(name,"unix_armor")){
		(*unixb).ap[cts[ct_unit_id]]=cts[ct_unix_armor];
	}else if(!strcmp(name,"unix_time")){
		(*unixb).b[cts[ct_unit_id]]=cts[ct_unix_time];
	}else if(!strcmp(name,"unix_mineral")){
		(*unixb).m[cts[ct_unit_id]]=cts[ct_unix_mineral];
	}else if(!strcmp(name,"unix_gas")){
		(*unixb).g[cts[ct_unit_id]]=cts[ct_unix_gas];
	}else if(!strcmp(name,"unix_name")){
		(*unixb).s[cts[ct_unit_id]]=cts[ct_unix_name];
	}else if(!strcmp(name,"unix_damage")){
		(*unixb).wb[cts[ct_unit_id]]=cts[ct_unix_damage];
	}else if(!strcmp(name,"unix_damagebonus")){
		(*unixb).wu[cts[ct_unit_id]]=cts[ct_unix_damagebonus];
	}else if(!strcmp(name,"upgx_default")){
		(*upgxb).d[cts[ct_upgrade]]=cts[ct_upgx_default];
	}else if(!strcmp(name,"upgx_mineral")){
		(*upgxb).mb[cts[ct_upgrade]]=cts[ct_upgx_mineral];
	}else if(!strcmp(name,"upgx_mineralfactor")){
		(*upgxb).mf[cts[ct_upgrade]]=cts[ct_upgx_mineralfactor];
	}else if(!strcmp(name,"upgx_gas")){
		(*upgxb).gb[cts[ct_upgrade]]=cts[ct_upgx_gas];
	}else if(!strcmp(name,"upgx_gasfactor")){
		(*upgxb).gf[cts[ct_upgrade]]=cts[ct_upgx_gasfactor];
	}else if(!strcmp(name,"upgx_time")){
		(*upgxb).tb[cts[ct_upgrade]]=cts[ct_upgx_time];
	}else if(!strcmp(name,"upgx_timefactor")){
		(*upgxb).tf[cts[ct_upgrade]]=cts[ct_upgx_timefactor];
	}else if(!strcmp(name,"tecx_default")){
		(*tecxb).d[cts[ct_technology]]=cts[ct_tecx_default];
	}else if(!strcmp(name,"tecx_mineral")){
		(*tecxb).m[cts[ct_technology]]=cts[ct_tecx_mineral];
	}else if(!strcmp(name,"tecx_gas")){
		(*tecxb).g[cts[ct_technology]]=cts[ct_tecx_gas];
	}else if(!strcmp(name,"tecx_time")){
		(*tecxb).t[cts[ct_technology]]=cts[ct_tecx_time];
	}else if(!strcmp(name,"tecx_energy")){
		(*tecxb).e[cts[ct_technology]]=cts[ct_tecx_energy];
	}else if(!strcmp(name,"cloaked")){
		unit_flags|=cloaked;
	}else if(!strcmp(name,"burrowed")){
		unit_flags|=burrowed;
	}else if(!strcmp(name,"intransit")){
		unit_flags|=intransit;
	}else if(!strcmp(name,"hallucinated")){
		unit_flags|=hallucinated;
	}else if(!strcmp(name,"invincible")){
		unit_flags|=invincible;
	}else if(!strcmp(name,"unit")){
		unit(cts[ct_player],cts[ct_unit_id],cts[ct_x],cts[ct_y],cts[ct_health],cts[ct_shield],cts[ct_energy],cts[ct_resource],cts[ct_hangar],unit_flags);
		unit_flags=0;
	}else if(!strcmp(name,"thg2_disabled")){
		thg2_flags|=1<<15;
	}else if(!strcmp(name,"thg2")){
		thg2(cts[ct_player],cts[ct_thg2_id],cts[ct_x],cts[ct_y],1<<7|1<<9|1<<12|thg2_flags);
		thg2_flags=0;
	}else if(!strcmp(name,"mask")){
		if(cts[ct_mask]){
			maskb[cts[ct_y]*(*dimb)[0]+cts[ct_x]]|=1<<cts[ct_player];
		}else{
			maskb[cts[ct_y]*(*dimb)[0]+cts[ct_x]]&=0xFF^1<<cts[ct_player];
		}
	}else if(!strcmp(name,"uprp")){
		uprpp(cts[ct_health],cts[ct_shield],cts[ct_energy],cts[ct_resource],cts[ct_hangar],unit_flags);
		unit_flags=0;
	}else if(!strcmp(name,"mrgn_low")){
		mrgn_flags|=low;
	}else if(!strcmp(name,"mrgn_medium")){
		mrgn_flags|=medium;
	}else if(!strcmp(name,"mrgn_high")){
		mrgn_flags|=high;
	}else if(!strcmp(name,"mrgn_lowair")){
		mrgn_flags|=lowair;
	}else if(!strcmp(name,"mrgn_mediumair")){
		mrgn_flags|=mediumair;
	}else if(!strcmp(name,"mrgn_highair")){
		mrgn_flags|=highair;
	}else if(!strcmp(name,"mrgn")){
		mrgn(cts[ct_x],cts[ct_y],cts[ct_xe],cts[ct_ye],cts[ct_mrgn_name],mrgn_flags);
		mrgn_flags=0;
	}else if(!strcmp(name,"sprp_name")){
		(*sprpb).n=cts[ct_sprp_name];
	}else if(!strcmp(name,"sprp_desc")){
		(*sprpb).d=cts[ct_sprp_desc];
	}else if(!strcmp(name,"forc_player")){
		(*forcb).p[cts[ct_player]]=cts[ct_forc_player];
	}else if(!strcmp(name,"forc_name")){
		(*forcb).s[cts[ct_force]]=cts[ct_forc_name];
	}else if(!strcmp(name,"forc_randomstartlocation")){
		(*forcb).f[cts[ct_force]]|=forc_random;
	}else if(!strcmp(name,"forc_allies")){
		(*forcb).f[cts[ct_force]]|=forc_allied;
	}else if(!strcmp(name,"forc_alliedvictory")){
		(*forcb).f[cts[ct_force]]|=forc_alliedvictory;
	}else if(!strcmp(name,"forc_sharedvision")){
		(*forcb).f[cts[ct_force]]|=forc_sharedvision;
	}else if(!strcmp(name,"wav_name")){
		wav(wav_path,wav_name);
	}else if(!strcmp(name,"colr")){
		(*colrb)[cts[ct_player]]=cts[ct_colr];
	}else if(!strcmp(name,"trig_group")){
		trig_groups|=1<<cts[ct_trig_group];
	}else if(!strcmp(name,"trig")){
		trig(trig_groups);
		trig_groups=0;
	}else if(!strcmp(name,"trigp")){
		trigp(trig_groups);
		trig_groups=0;
	}else if(!strcmp(name,"condition")){
		condition(cts[ct_cond_l],cts[ct_cond_g],cts[ct_cond_n],cts[ct_cond_u],cts[ct_cond_m],cts[ct_cond_c],cts[ct_cond_r],cts[ct_cond_f]);
	}else if(!strcmp(name,"action")){
		if(cts[ct_act_c]==60){
			displaytextmessagea(cts[ct_act_s]);
		}else{
			action(cts[ct_act_l],cts[ct_act_s],cts[ct_act_w],cts[ct_act_t],cts[ct_act_gf],cts[ct_act_gs],cts[ct_act_u],cts[ct_act_c],cts[ct_act_n],cts[ct_act_f]);
		}
	}else if(!strcmp(name,"mbrf")){
		mbrf(trig_groups);
		trig_groups=0;
	}else if(!strcmp(name,"mbrf_action")){
		mbrf_action(cts[ct_act_l],cts[ct_act_s],cts[ct_act_w],cts[ct_act_t],cts[ct_act_gf],cts[ct_act_gs],cts[ct_act_u],cts[ct_act_c],cts[ct_act_n],cts[ct_act_f]);
	}
	data=0;
}
void XMLCALL
charData(void *userData,const XML_Char *string,int len){
	if(!data)return;
	char s[len+1];
	memcpy(s,string,len);
	s[len]=0;
	int i=0;
	if(ct==ct_unix_name||ct==ct_mrgn_name||ct==ct_sprp_name||ct==ct_sprp_desc||ct==ct_forc_name||ct==ct_act_s||ct==ct_act_w){
		i=mint_str(s);
		goto charData0;
	}else if(ct==ct_cond_l||ct==ct_act_l){
		i=mrgns(mint_str(s));
		goto charData0;
	}else if(ct==ct_act_gs){
		if(cts[ct_act_c]==38||cts[ct_act_c]==39||cts[ct_act_c]==46){
			i=mrgns(mint_str(s));
		}else if(cts[ct_act_c]==15||cts[ct_act_c]==16){
			i=*(int32_t *)s;
		}else{
			char *se;
			i=strtoll(s,&se,0);
			if(se!=s+len){
				for(int j=0;j<27;j++){
					if(!strcmp(s,str_groups[j])){
						i=j;
						goto charData0;
					}
				}
			}
		}
		goto charData0;
	}else if(ct==ct_wav_path){
		strcpy(wav_path,s);
		goto charData0;
	}else if(ct==ct_wav_name){
		strcpy(wav_name,s);
		goto charData0;
	}else{
		char *se;
		i=strtoll(s,&se,0);
		if(se!=s+len){
			for(int j=0;j<3;j++){
				if(!strcmp(s,str_res[j])){
					i=j;
					goto charData0;
				}
			}
			for(int j=0;j<3;j++){
				if(!strcmp(s,str_ally[j])){
					i=j;
					goto charData0;
				}
			}
			for(int j=0;j<3;j++){
				if(!strcmp(s,str_order[j])){
					i=j;
					goto charData0;
				}
			}
			for(int j=0;j<4;j++){
				if(!strcmp(s,str_forc[j])){
					if(ct==ct_force){
						(*forcb).f[j]=0;
					}
					i=j;
					goto charData0;
				}
			}
			for(int j=0;j<8;j++){
				if(!strcmp(s,str_score[j])){
					i=j;
					goto charData0;
				}
			}
			for(int j=0;j<8;j++){
				if(!strcmp(s,str_era[j])){
					i=j;
					goto charData0;
				}
			}
			for(int j=0;j<8;j++){
				if(!strcmp(s,str_side[j])){
					i=j;
					goto charData0;
				}
			}
			for(int j=0;j<9;j++){
				if(!strcmp(s,str_ownr[j])){
					i=j;
					goto charData0;
				}
			}
			for(int j=0;j<10;j++){
				if(!strcmp(s,str_mbrf[j])){
					i=j;
					goto charData0;
				}
			}
			for(int j=0;j<12;j++){
				if(!strcmp(s,str_mod[j])){
					i=j;
					goto charData0;
				}
			}
			for(int j=0;j<18;j++){
				if(!strcmp(s,str_colr[j])){
					i=j;
					goto charData0;
				}
			}
			for(int j=0;j<24;j++){
				if(!strcmp(s,str_conditions[j])){
					i=j;
					goto charData0;
				}
			}
			for(int j=0;j<27;j++){
				if(!strcmp(s,str_groups[j])){
					i=j;
					goto charData0;
				}
			}
			for(int j=0;j<44;j++){
				if(!strcmp(s,str_tech[j])){
					i=j;
					goto charData0;
				}
			}
			for(int j=0;j<60+1;j++){
				if(!strcmp(s,str_actions[j])){
					i=j;
					goto charData0;
				}
			}
			for(int j=0;j<61;j++){
				if(!strcmp(s,str_upg[j])){
					i=j;
					goto charData0;
				}
			}
			for(int j=0;j<130;j++){
				if(!strcmp(s,str_weap[j])){
					i=j;
					goto charData0;
				}
			}
			for(int j=0;j<228+5;j++){
				if(!strcmp(s,str_units[j])){
					i=j;
					goto charData0;
				}
			}
		}
	}
charData0:
	cts[ct]=i;
}
void
scmc(void){
	memset(uprpb,0,sizeof(*uprpb));
	
	XML_Parser parser=XML_ParserCreate(NULL);
	XML_SetElementHandler(parser,startElement,endElement);
	XML_SetCharacterDataHandler(parser,charData);
	FILE *xml=fopen(argv[3],"rb");
	fseek(xml,0,SEEK_END);
	long xmlSize=ftell(xml);
	rewind(xml);
	char *xmlbuf=(char *)malloc(xmlSize);
	int len=fread(xmlbuf,1,xmlSize,xml);
	XML_Parse(parser,xmlbuf,len,1);
	XML_ParserFree(parser);
}
int
main(int ac,char **av){
	argc=ac;
	argv=av;
	return(scmc_main(3,av,scmc));
}