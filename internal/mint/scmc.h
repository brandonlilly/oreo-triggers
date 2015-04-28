#ifndef SCMC_H
#define SCMC_H

#ifdef SCMC_EXPORT
#define SCMC __declspec(dllexport)
#else
#define SCMC __declspec(dllimport)
#endif

#include<stdint.h>

#define SCMC_MAIN \
void scmc(void); \
int main(int ac,char **av){ \
	return(scmc_main(ac,av,scmc)); \
}

#define DEFAULT "\x01"
#define CYAN "\x02"
#define YELLOW "\x03"
#define WHITE "\x04"
#define GREY "\x05"
#define RED "\x06"
#define GREEN "\x07"
#define RED_P1 "\x08"
#define TAB "\x09"
#define INVISIBLE "\x0B"
#define REMOVE "\x0C"
#define BLUE_P2 "\x0E"
#define TEAL_P3 "\x0F"
#define PURPLE_P4 "\x10"
#define ORANGE_P5 "\x11"
#define RIGHT "\x12"
#define CENTER "\x13"
#define INVISIBLE2 "\x14"
#define BROWN_P6 "\x15"
#define WHITE_P7 "\x16"
#define YELLOW_P8 "\x17"
#define GREEN_P9 "\x18"
#define BRIGHTYELLOW_P10 "\x19"
#define CYAN2 "\x1A"
#define PINKISH_P11 "\x1B"
#define DARKCYAN_P12 "\x1C"
#define GREYGREEN "\x1D"
#define BLUEGREY "\x1E"
#define TURQUOISE "\x1F"

enum group{
	p1=0,
	p2=1,
	p3=2,
	p4=3,
	p5=4,
	p6=5,
	p7=6,
	p8=7,
	p9=8,
	p10=9,
	p11=10,
	p12=11,
	pnone=12,
	current=13,
	foes=14,
	allies=15,
	neutral=16,
	pall=17,
	f1=18,
	f2=19,
	f3=20,
	f4=21,
	u1=22,
	u2=23,
	u3=24,
	u4=25,
	nonalliedvictory=26,
};
enum modifier{
	atleast=0,
	atmost=1,
	isset=2,
	isclear=3,
	toset=4,
	toclear=5,
	toggle=6,
	setto=7,
	add=8,
	subtract=9,
	exactly=10,
	_random=11,
};
enum resource{
	ore=0,
	gas=1,
	oreandgas=2,
};
enum ally{
	enemy=0,
	ally=1,
	alliedvictory=2,
};
enum order{
	move=0,
	patrol=1,
	attack=2,
};
enum score{
	total=0,
	units=1,
	buildings=2,
	unitsandbuildings=3,
	kills=4,
	razings=5,
	killsandrazings=6,
	custom=7,
};
enum state{
	cloaked=1<<0,
	burrowed=1<<1,
	intransit=1<<2,
	hallucinated=1<<3,
	invincible=1<<4,
};
enum number{
	all=0,
};
enum location{
	low=1<<0,
	medium=1<<1,
	high=1<<2,
	lowair=1<<3,
	mediumair=1<<4,
	highair=1<<5,
};
enum ownr{
	ownr_inactive=0,
	ownr_computer2=1,
	ownr_human2=2,
	ownr_rescue=3,
	ownr_unused=4,
	ownr_computer=5,
	ownr_human=6,
	ownr_neutral=7,
	ownr_closed=8,
};
enum unite{
	marine=0,
	ghost=1,
	vulture=2,
	goliath=3,
	goliathturret=4,
	siegetank_tankmode=5,
	siegetankturret_tankmode=6,
	scv=7,
	wraith=8,
	sciencevessel=9,
	guimontag=10,
	dropship=11,
	battlecruiser=12,
	spidermine=13,
	nuclearmissile=14,
	civilian=15,
	sarahkerrigan=16,
	alanschezar=17,
	alanschezarturret=18,
	jimraynor_vulture=19,
	jimraynor_marine=20,
	tomkazansky=21,
	magellan=22,
	edmundduke_tankmode=23,
	edmundduketurret_tankmode=24,
	edmundduke_siegemode=25,
	edmundduketurret_siegemode=26,
	arcturusmengsk=27,
	hyperion=28,
	noradii_battlecruiser=29,
	siegetank_siegemode=30,
	siegetankturret_siegemode=31,
	firebat=32,
	scannersweep=33,
	medic=34,
	larva=35,
	egg=36,
	zergling=37,
	hydralisk=38,
	ultralisk=39,
	broodling=40,
	drone=41,
	overlord=42,
	mutalisk=43,
	guardian=44,
	queen=45,
	defiler=46,
	scourge=47,
	torrasque=48,
	matriarch=49,
	infestedterran=50,
	infestedkerrigan=51,
	uncleanone=52,
	hunterkiller=53,
	devouringone=54,
	kukulza_mutalisk=55,
	kukulza_guardian=56,
	yggdrasill=57,
	valkyrie=58,
	mutaliskcocoon=59,
	corsair=60,
	darktemplar_unit=61,
	devourer=62,
	darkarchon=63,
	probe=64,
	zealot=65,
	dragoon=66,
	hightemplar=67,
	archon=68,
	shuttle=69,
	scout=70,
	arbiter=71,
	carrier=72,
	interceptor=73,
	darktemplar_hero=74,
	zeratul=75,
	tassadarzeratul=76,
	fenix_zealot=77,
	fenix_dragoon=78,
	tassadar=79,
	mojo=80,
	warbringer=81,
	gantrithor=82,
	reaver=83,
	observer=84,
	scarab=85,
	danimoth=86,
	aldaris_templar=87,
	artanis_scout=88,
	rhynadon=89,
	bengalaas=90,
	cargoship=91,
	mercenarygunship=92,
	scantid=93,
	kakaru=94,
	ragnasaur=95,
	ursadon=96,
	lurkeregg=97,
	raszagal=98,
	samirduran=99,
	alexeistukov=100,
	maprevealer=101,
	gerarddugalle=102,
	lurker=103,
	infestedduran=104,
	disruptionweb=105,
	commandcenter=106,
	comsatstation=107,
	nuclearsilo=108,
	supplydepot=109,
	refinery=110,
	barracks=111,
	academy=112,
	factory=113,
	starport=114,
	controltower=115,
	sciencefacility=116,
	covertops=117,
	physicslab=118,
	starbase=119,
	machineshop=120,
	repairbay=121,
	engineeringbay=122,
	armory=123,
	missileturret=124,
	bunker=125,
	noradii_crashed=126,
	ioncannon=127,
	urajcrystal=128,
	khaliscrystal=129,
	infestedcommandcenter=130,
	hatchery=131,
	lair=132,
	hive=133,
	nyduscanal=134,
	hydraliskden=135,
	defilermound=136,
	greaterspire=137,
	queensnest=138,
	evolutionchamber=139,
	ultraliskcavern=140,
	spire=141,
	spawningpool=142,
	creepcolony=143,
	sporecolony=144,
	unusedzergbuilding1=145,
	sunkencolony=146,
	overmind_withshell=147,
	overmind=148,
	extractor=149,
	maturechrysalis=150,
	cerebrate=151,
	cerebratedaggoth=152,
	unusedzergbuilding2=153,
	nexus=154,
	roboticsfacility=155,
	pylon=156,
	assimilator=157,
	unusedprotossbuilding1=158,
	observatory=159,
	gateway=160,
	unusedprotossbuilding2=161,
	photoncannon=162,
	citadelofadun=163,
	cyberneticscore=164,
	templararchives=165,
	forge=166,
	stargate=167,
	statiscellprison=168,
	fleetbeacon=169,
	arbitertribunal=170,
	roboticssupportbay=171,
	shieldbattery=172,
	khaydarincrystalformation=173,
	temple=174,
	xelnagatemple=175,
	mineralfield_type1=176,
	mineralfield_type2=177,
	mineralfield_type3=178,
	cave=179,
	cavein=180,
	cantina=181,
	miningplatform=182,
	independentcommandcenter=183,
	independentstarport=184,
	independentjumpgate=185,
	ruins=186,
	khaydarincrystalformation_unused=187,
	vespenegeyser=188,
	warpgate=189,
	psidisruptor=190,
	zergmarker=191,
	terranmarker=192,
	protossmarker=193,
	zergbeacon=194,
	terranbeacon=195,
	protossbeacon=196,
	zergflagbeacon=197,
	terranflagbeacon=198,
	protossflagbeacon=199,
	powergenerator=200,
	overmindcocoon=201,
	darkswarm=202,
	floormissiletrap=203,
	floorhatch=204,
	leftupperleveldoor=205,
	rightupperleveldoor=206,
	leftpitdoor=207,
	rightpitdoor=208,
	floorguntrap=209,
	leftwallmissiletrap=210,
	leftwallflametrap=211,
	rightwallmissiletrap=212,
	rightwallflametrap=213,
	startlocation=214,
	flag=215,
	youngchrysalis=216,
	psiemitter=217,
	datadisc=218,
	khaydarincrystal=219,
	mineralclustertype1=220,
	mineralclustertype2=221,
	vespenegasorbtype1=222,
	vespenegasorbtype2=223,
	vespenegassactype1=224,
	vespenegassactype2=225,
	vespenegastanktype1=226,
	vespenegastanktype2=227,
	none=228,
	any=229,
	anymen=230,
	anybuildings=231,
	anyfactories=232,
};
enum upgrade{
	upgr_terraninfantryarmor=0,
	upgr_terranvehicleplating=1,
	upgr_terranshipplating=2,
	upgr_zergcarapace=3,
	upgr_zergflyercarapace=4,
	upgr_protossarmor=5,
	upgr_protossplating=6,
	upgr_terraninfantryweapons=7,
	upgr_terranvehicleweapons=8,
	upgr_terranshipweapons=9,
	upgr_zergmeleeattacks=10,
	upgr_zergmissileattacks=11,
	upgr_zergflyerattacks=12,
	upgr_protossgroundweapons=13,
	upgr_protossairweapons=14,
	upgr_protossplasmashields=15,
	upgr_u238shells=16,
	upgr_ionthrusters=17,
	upgr_burstlasers=18,
	upgr_titanreactor=19,
	upgr_ocularimplants=20,
	upgr_moebiusreactor=21,
	upgr_apolloreactor=22,
	upgr_colossusreactor=23,
	upgr_ventralsacs=24,
	upgr_antennae=25,
	upgr_pneumatizedcarapace=26,
	upgr_metabolicboost=27,
	upgr_adrenalglands=28,
	upgr_muscularaugments=29,
	upgr_groovedspines=30,
	upgr_gametemeiosis=31,
	upgr_metasynapticnode=32,
	upgr_singularitycharge=33,
	upgr_legenhancements=34,
	upgr_scarabdamage=35,
	upgr_reavercapacity=36,
	upgr_graviticdrive=37,
	upgr_sensorarray=38,
	upgr_graviticboosters=39,
	upgr_khaydarinamulet=40,
	upgr_apialsensors=41,
	upgr_graviticthrusters=42,
	upgr_carriercapacity=43,
	upgr_khaydarincore=44,
	upgr_argusjewel=47,
	upgr_argustalisman=49,
	upgr_caduceusreactor=51,
	upgr_chitinousplating=52,
	upgr_anabolicsynthesis=53,
	upgr_charonbooster=54,
	upgr_upgrade60=60,
};
enum technology{
	tech_stimpacks=0,
	tech_lockdown=1,
	tech_empshockwave=2,
	tech_spidermines=3,
	tech_scannersweep=4,
	tech_tanksiegemode=5,
	tech_defensivematrix=6,
	tech_irradiate=7,
	tech_yamatogun=8,
	tech_cloakingfield=9,
	tech_personnelcloaking=10,
	tech_burrowing=11,
	tech_infestation=12,
	tech_spawnbroodling=13,
	tech_darkswarm=14,
	tech_plague=15,
	tech_consume=16,
	tech_ensnare=17,
	tech_parasite=18,
	tech_psionicstorm=19,
	tech_hallucination=20,
	tech_recall=21,
	tech_statisfield=22,
	tech_archonwarp=23,
	tech_restoration=24,
	tech_disruptionweb=25,
	tech_mindcontrol=27,
	tech_darkarchonmeld=28,
	tech_feedback=29,
	tech_opticalflare=30,
	tech_maelstrom=31,
	tech_lurkeraspect=32,
	tech_healing=34,
};
enum weapon{
	weap_gaussrifle=0,
	weap_gaussrifle_jimraynor=1,
	weap_c10concussionrifle=2,
	weap_c10concussionrifle_sarahkerrigan=3,
	weap_fragmentationgrenade=4,
	weap_fragmentationgrenade_jimraynor=5,
	weap_spidermines=6,
	weap_twinautocannons=7,
	weap_hellfiremissilepack=8,
	weap_twinautocannons_alanschezar=9,
	weap_hellfiremissilepack_alanschezar=10,
	weap_arclitecannon=11,
	weap_arclitecannon_edmundduke=12,
	weap_fusioncutter=13,
	weap_fusioncutter_harvest=14,
	weap_geminimissiles=15,
	weap_burstlasers=16,
	weap_geminimissiles_tomkazansky=17,
	weap_burstlasers_tomkazansky=18,
	weap_atslaserbattery=19,
	weap_atalaserbattery=20,
	weap_atslaserbattery_noradii_mengsk_dugalle=21,
	weap_atalaserbattery_noradii_mengsk_dugalle=22,
	weap_atslaserbattery_hyperion=23,
	weap_atalaserbattery_hyperion=24,
	weap_flamethrower=25,
	weap_flamethrower_guimontag=26,
	weap_arcliteshockcannon=27,
	weap_arcliteshockcannon_edmundduke=28,
	weap_longboltmissiles=29,
	weap_yamatogun=30,
	weap_nuclearmissile=31,
	weap_lockdown=32,
	weap_empshockwave=33,
	weap_irradiate=34,
	weap_claws=35,
	weap_claws_devouringone=36,
	weap_claws_infestedkerrigan=37,
	weap_needlespines=38,
	weap_needlespines_hunterkiller=39,
	weap_kaiserblades=40,
	weap_kaiserblades_torrasque=41,
	weap_toxicspores=42,
	weap_spines=43,
	weap_spines_harvest=44,
	weap_acidspore=46,
	weap_acidspore_kukulza=47,
	weap_glavewurm=48,
	weap_glavewurm_kukulza=49,
	weap_seekerspores=52,
	weap_subterraneantentacle=53,
	weap_suicide_infestedterran=54,
	weap_suicide_scourge=55,
	weap_parasite=56,
	weap_spawnbroodlings=57,
	weap_ensnare=58,
	weap_darkswarm=59,
	weap_plague=60,
	weap_consume=61,
	weap_particlebeam=62,
	weap_particlebeam_harvest=63,
	weap_psiblades=64,
	weap_psiblades_fenix=65,
	weap_phasedisruptor=66,
	weap_phasedisruptor_fenix=67,
	weap_psiassault=69,
	weap_psionicshockwave=70,
	weap_psionicshockwave_tassadarzeratul=71,
	weap_dualphotonblasters=73,
	weap_antimattermissiles=74,
	weap_dualphotonblasters_mojo=75,
	weap_antimattermissiles_mojo=76,
	weap_phasedisruptorcannon=77,
	weap_phasedisruptorcannon_danimoth=78,
	weap_pulsecannon=79,
	weap_stsphotoncannon=80,
	weap_staphotoncannon=81,
	weap_scarab=82,
	weap_statisfield=83,
	weap_psistorm=84,
	weap_warpblades_zeratul=85,
	weap_warpblades_darktemplar_hero=86,
	weap_twinautocannons_floortrap=96,
	weap_hellfiremissilepack_walltrap=97,
	weap_flamethrower_walltrap=98,
	weap_hellfiremissilepack_floortrap=99,
	weap_neutronflare=100,
	weap_disruptionweb=101,
	weap_restoration=102,
	weap_halorockets=103,
	weap_corrosiveacid=104,
	weap_mindcontrol=105,
	weap_feedback=106,
	weap_opticalflare=107,
	weap_maelstrom=108,
	weap_subterraneanspines=109,
	weap_warpblades=111,
	weap_c10concussionrifle_samirduran=112,
	weap_c10concussionrifle_infestedduran=113,
	weap_dualphotonblasters_artanis=114,
	weap_antimattermissiles_artanis=115,
	weap_c10concussionrifle_alexeistukov=116,
};
enum force{
	forc_random=1<<0,
	forc_allied=1<<1,
	forc_alliedvictory=1<<2,
	forc_sharedvision=1<<3,
};
enum era{
	badlands=0,
	spaceplatform=1,
	installation=2,
	ashworld=3,
	jungle=4,
	desert=5,
	arctic=6,
	twilight=7,
};
enum side{
	side_zerg=0,
	side_terran=1,
	side_protoss=2,
	side_independent=3,
	side_neutral=4,
	side_userselectable=5,
	side_random=6,
	side_inactive=7,
};
enum colr{
	red=0,
	blue=1,
	teal=2,
	purple=3,
	orange=4,
	brown=5,
	white=6,
	yellow=7,
	green=8,
	paleyellow=9,
	_tan=10,
	neutralcolor=11,
	palegreen=12,
	blueishgray=13,
	paleyellow2=14,
	cyan=15,
	black=17,
};

#pragma pack(push,1)

/*
 * Description: Initiate SCMC. Shall be the first SCMC function called.
 * Arguments:   argc,argv,SCMC callback
 * Return:      0 on success
 */
SCMC int scmc_main(int ac,char **av,void (*scmc)(void));

/*
 * Description: Player controllers.
 */
SCMC extern int8_t (*ownrb)[12];

/*
 * Description: Tileset.
 */
SCMC extern int16_t *erab;

/*
 * Description: Map dimensions.
 */
SCMC extern int16_t (*dimb)[2];

/*
 * Description: Races.
 */
SCMC extern int8_t (*sideb)[12];

/*
 * Description: Terrain tiles.
 */
SCMC extern int16_t *mtxmb;

/*
 * Description: Unit restrictions.
 * Members: Available,Default available,Default
 */
SCMC extern struct puni{
	int8_t a[12][228];
	int8_t ad[228];
	int8_t d[12][228];
}*punib;

/*
 * Description: Upgrade restrictions.
 * Members:     Max,Base,Default max,Default base,Default
 */
SCMC extern struct pupx{
	int8_t m[12][61];
	int8_t b[12][61];
	int8_t md[61];
	int8_t bd[61];
	int8_t d[12][61];
}*pupxb;

/*
 * Description: Technology restrictions.
 * Members:     Availability,Researched,Default availability,Default researched,Default
 */
SCMC extern struct ptex{
	int8_t a[12][44];
	int8_t r[12][44];
	int8_t ad[44];
	int8_t rd[44];
	int8_t d[12][44];
}*ptexb;

/*
 * Description: Create a unit.
 * Arguments:   Player,Unit,X,Y,Health,Shield,Energy,Resource,Hangar,Flags
 */
SCMC void unit(int8_t p,int16_t i,int16_t x,int16_t y,int8_t hp,int8_t sp,int8_t ep,int32_t r,int16_t h,int16_t f);

/*
 * Description: Create a sprite.
 * Arguments:   Player,Sprite,X,Y,Flags
 */
SCMC void thg2(int8_t p,int16_t i,int16_t x,int16_t y,int16_t f);

/*
 * Description: Fog of war.
 */
SCMC extern int8_t *maskb;

/*
 * Description: String chunk size.
 */
SCMC extern uint32_t strs;
/*
 * Description: String count.
 */
SCMC extern uint16_t *strc;
/*
 * Description: Create/obtain a string.
 * Arguments:   C string
 * Return:      String
 */
SCMC int16_t str(const char *s);

/*
 * Description: Unit properties
 * Members:     Valid flags,Valid elements,Player,Health,Shield,Energy,Resource,Hangar,Flags
 */
SCMC extern struct uprp{
	int16_t vf;
	int16_t ve;
	int8_t p;
	int8_t hp;
	int8_t sp;
	int8_t ep;
	int32_t r;
	int16_t h;
	int16_t f;
	int8_t u[4];
}*uprpb;
/*
 * Description: Create/obtain a unit property entry.
 * Arguments:   Health,Shield,Energy,Resource,Hangar,Flags
 * Return:      Unit property
 */
SCMC int32_t uprpp(int8_t hp,int8_t sp,int8_t ep,int32_t r,int16_t h,int16_t f);

/*
 * Description: Location count limit.
 */
SCMC extern uint8_t mrgnn;
/*
 * Description: Locations.
 * Members: X,Y,X end,Y end,String,Flags
 */
SCMC extern struct mrgn{
	int32_t x;
	int32_t y;
	int32_t xe;
	int32_t ye;
	int16_t s;
	int16_t f;
}*mrgnb;
/*
 * Description: Create a location.
 * Arguments:   X,Y,X end,Y end,Location string,Flags
 * Return:      Location
 */
SCMC uint8_t mrgn(int32_t x,int32_t y,int32_t xe,int32_t ye,int16_t s,int16_t f);
/*
 * Description: Obtain a location.
 * Arguments:   Location string
 * Return:      Location
 */
SCMC uint8_t mrgns(int16_t s);

/*
 * Description: Create a trigger.
 * Arguments:   Owner group
 */
SCMC void trig(int32_t g);
/*
 * Description: Create a preserved (flag) trigger.
 * Arguments:   Owner group
 */
SCMC void trigp(int32_t g);

/*
 * Description: Create a mission briefing trigger.
 * Arguments:   Owner group
 */
SCMC void mbrf(int32_t g);

/*
 * Description: Scenario properties.
 * Members:     Name,Description
 */
SCMC extern struct sprp{
	int16_t n;
	int16_t d;
}*sprpb;

/*
 * Description: Forces.
 * Members:     Player,String,Flags
 */
SCMC extern struct forc{
	int8_t p[8];
	int16_t s[4];
	int8_t f[4];
}*forcb;

/*
 * Description: Add a WAV.
 * Arguments:   Path,MPQ path
 */
SCMC void wav(const char *p,const char *pm);

/*
 * Description: Unit settings.
 * Members:     Default,Health,Shield,Armor,Build time,Minerals,Gas,String,Weapon base,Weapon upgrade
 */
SCMC extern struct unix{
	int8_t d[228];
	int32_t hp[228];
	int16_t sp[228];
	int8_t ap[228];
	int16_t b[228];
	int16_t m[228];
	int16_t g[228];
	int16_t s[228];
	int16_t wb[130];
	int16_t wu[130];
}*unixb;

/*
 * Description: Upgrade settings.
 * Members:     Default,Unused,Mineral base,Mineral factor,Gas base,Gas factor,Time base,Time factor
 */
SCMC extern struct upgx{
	int8_t d[61];
	int8_t u;
	int16_t mb[61];
	int16_t mf[61];
	int16_t gb[61];
	int16_t gf[61];
	int16_t tb[61];
	int16_t tf[61];
}*upgxb;

/*
 * Description: Technology settings.
 * Members:     Default,Minerals,Gas,Time,Energy
 */
SCMC extern struct tecx{
	int8_t d[44];
	int16_t m[44];
	int16_t g[44];
	int16_t t[44];
	int16_t e[44];
}*tecxb;

/*
 * Description: Player colors.
 */
SCMC extern uint8_t (*colrb)[8];

#pragma pack(pop)

SCMC void condition(int32_t l,int32_t g,int32_t n,int16_t u,int8_t m,int8_t c,int8_t r,int8_t f);
//Group,Resource,Modifier,Number
SCMC void accumulate(int32_t g,int8_t r,int8_t m,int32_t n);
//Group,Unit,Modifier,Number,Location
SCMC void bring(int32_t g,int16_t u,int8_t m,int32_t n,int32_t l);
//Group,Unit,Modifier,Number
SCMC void command(int32_t g,int16_t u,int8_t m,int32_t n);
//Unit
SCMC void commandtheleast(int16_t u);
//Unit,Location
SCMC void commandtheleastat(int16_t u,int32_t l);
//Unit
SCMC void commandthemost(int16_t u);
//Unit,Location
SCMC void commandthemostat(int16_t u,int32_t l);
//Modifier,Game seconds
SCMC void countdowntimer(int8_t m,int32_t n);
//Group,Unit,Modifier,Number
SCMC void deaths(int32_t g,int16_t u,int8_t m,int32_t n);
//Modifier,Game seconds
SCMC void elapsedtime(int8_t m,int32_t n);
//Score
SCMC void highestscore(int8_t r);
//Group,Unit,Modifier,Number
SCMC void kill(int32_t g,int16_t u,int8_t m,int32_t n);
//Unit
SCMC void leastkills(int16_t u);
//Resource
SCMC void leastresources(int8_t r);
//Score
SCMC void lowestscore(int8_t r);
//Unit
SCMC void mostkills(int16_t u);
//Resource
SCMC void mostresources(int8_t r);
SCMC void never(void);
//Group,Modifier,Number
SCMC void opponents(int32_t g,int8_t m,int32_t n);
//Group,Score,Modifier,Number
SCMC void score(int32_t g,int8_t r,int8_t m,int32_t n);
//Switch,State
SCMC void _switch(int8_t r,int8_t m);

SCMC void action(int32_t l,int32_t s,int32_t w,int32_t t,int32_t gf,int32_t gs,int16_t u,int8_t c,int8_t n,int8_t f);
//Location
SCMC void centerview(int32_t l);
//Group,Unit,Number,Location
SCMC void createunit(int32_t gf,int16_t u,int8_t n,int32_t l);
//Group,Unit,Number,Location,Unit property
SCMC void createunitwithproperties(int32_t gf,int16_t u,int8_t n,int32_t l,int32_t gs);
SCMC void defeat(void);
//String
SCMC void displaytextmessage(int32_t s);
//String
SCMC void displaytextmessagea(int32_t s);
SCMC void draw(void);
//Group,Unit,Latter group,Number,Location
SCMC void giveunitstoplayer(int32_t gf,int16_t u,int32_t gs,int8_t n,int32_t l);
//Group,Unit
SCMC void killunit(int32_t gf,int16_t u);
//Group,Unit,Number,Location
SCMC void killunitatlocation(int32_t gf,int16_t u,int8_t n,int32_t l);
//Unit,String
SCMC void leaderboard_control(int16_t u,int32_t s);
//Unit,Location,String
SCMC void leaderboard_controlatlocation(int16_t u,int32_t l,int32_t s);
//Number
SCMC void leaderboard_greed(int32_t gs);
//Unit,String
SCMC void leaderboard_kills(int16_t u,int32_t s);
//Score,String
SCMC void leaderboard_points(int16_t u,int32_t s);
//Resource,String
SCMC void leaderboard_resources(int16_t u,int32_t s);
//State
SCMC void leaderboardcomputerplayers(int8_t n);
//Unit,Number,String
SCMC void leaderboardgoal_control(int16_t u,int32_t gs,int32_t s);
//Unit,Number,Location,String
SCMC void leaderboardgoal_controlatlocation(int16_t u,int32_t gs,int32_t l,int32_t s);
//Unit,Number,String
SCMC void leaderboardgoal_kills(int16_t u,int32_t gs,int32_t s);
//Score,Number,String
SCMC void leaderboardgoal_points(int16_t u,int32_t gs,int32_t s);
//Resource,Number,String
SCMC void leaderboardgoal_resources(int16_t u,int32_t gs,int32_t s);
//Location
SCMC void minimapping(int32_t l);
//Group,Unit,Number,Location,Percent
SCMC void modifyenergy(int32_t gf,int16_t u,int8_t n,int32_t l,int32_t gs);
//Group,Unit,Number,Location,Hangar amount
SCMC void modifyhangarcount(int32_t gf,int16_t u,int8_t n,int32_t l,int32_t gs);
//Group,Unit,Number,Location,Percent
SCMC void modifyhitpoints(int32_t gf,int16_t u,int8_t n,int32_t l,int32_t gs);
//Group,Number,Location,Resource amount
SCMC void modifyresourceamount(int32_t gf,int8_t n,int32_t l,int32_t gs);
//Group,Unit,Number,Location,Percent
SCMC void modifyshieldpoints(int32_t gf,int16_t u,int8_t n,int32_t l,int32_t gs);
//Group,Unit,Location,Latter location
SCMC void movelocation(int32_t gf,int16_t u,int32_t l,int32_t gs);
//Group,Unit,Number,Location,Latter location
SCMC void moveunit(int32_t gf,int16_t u,int8_t n,int32_t l,int32_t gs);
SCMC void muteunitspeech(void);
//Group,Unit,Location,Latter location,Order
SCMC void order(int32_t gf,int16_t u,int32_t l,int32_t gs,int8_t n);
SCMC void pausegame(void);
SCMC void pausetimer(void);
//WAV
SCMC void playwav(int32_t w);
//Group,Unit
SCMC void removeunit(int32_t gf,int16_t u);
//Group,Unit,Number,Location
SCMC void removeunitatlocation(int32_t gf,int16_t u,int8_t n,int32_t l);
//AI Script
SCMC void runaiscript(int32_t gs);
//AI Script,Location
SCMC void runaiscriptatlocation(int32_t gs,int32_t l);
//Group,Status
SCMC void setalliancestatus(int32_t gf,int16_t u);
//Modifier,Game seconds
SCMC void setcountdowntimer(int8_t n,int32_t t);
//Group,Unit,Modifier,Amount
SCMC void setdeaths(int32_t gf,int16_t u,int8_t n,int32_t gs);
//Group,Unit,Location,State
SCMC void setdoodadstate(int32_t gf,int16_t u,int32_t l,int8_t n);
//Group,Unit,Location,State
SCMC void setinvincibility(int32_t gf,int16_t u,int32_t l,int8_t n);
//String
SCMC void setmissionobjectives(int32_t s);
//String
SCMC void setnextscenario(int32_t s);
//Group,Unit,Modifier,Amount
SCMC void setresources(int32_t gf,int16_t u,int8_t n,int32_t gs);
//Group,Score,Modifier,Amount
SCMC void setscore(int32_t gf,int16_t u,int8_t n,int32_t gs);
//Switch,State
SCMC void setswitch(int32_t gs,int8_t n);
//Unit,Milliseconds
SCMC void talkingportrait(int16_t u,int32_t t);
//Unit,Location,WAV,Modifier,Milliseconds,String
SCMC void transmission(int16_t u,int32_t l,int32_t w,int8_t n,int32_t gs,int32_t s);
SCMC void unmuteunitspeech(void);
SCMC void unpausegame(void);
SCMC void unpausetimer(void);
SCMC void victory(void);
//Milliseconds
SCMC void wait(int32_t t);

SCMC void mbrf_action(int32_t l,int32_t s,int32_t w,int32_t t,int32_t gf,int32_t gs,int16_t u,int8_t c,int8_t n,int8_t f);
//Slot,Milliseconds
SCMC void mbrf_displayspeakingportrait(int32_t gf,int32_t t);
//Slot
SCMC void mbrf_hideportrait(int32_t gf);
//String
SCMC void mbrf_missionobjectives(int32_t s);
//WAV
SCMC void mbrf_playwav(int32_t w);
//Unit,Slot
SCMC void mbrf_showportrait(int16_t u,int32_t gf);
SCMC void mbrf_skiptutorialenabled(void);
//String,Milliseconds
SCMC void mbrf_textmessage(int32_t s,int32_t t);
//Slot,WAV,Modifier,Milliseconds,String
SCMC void mbrf_transmission(int32_t gf,int32_t w,int8_t n,int32_t gs,int32_t s);
//Milliseconds
SCMC void mbrf_wait(int32_t t);

#endif
