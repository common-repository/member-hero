/*!
 * EmojioneArea v3.4.1
 * https://github.com/mervick/emojionearea
 * Copyright Andrey Izman and other contributors
 * Released under the MIT license
 * Date: 2018-04-27T09:03Z
 */
window = ( typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {} );
document = window.document || {};

; ( function ( factory, global ) {
    if ( typeof require === "function" && typeof exports === "object" && typeof module === "object" ) {

        // CommonJS
        factory( require( "jquery" ) );
    } else if ( typeof define === "function" && define.amd ) {

        // AMD
        define( [ "jquery" ], factory );
    } else {

        // Normal script tag
        factory( global.jQuery );
    }
}( function ( $ ) {
    "use strict";

    var unique = 0;
    var eventStorage = {};
    var possibleEvents = {};
    var emojione = window.emojione;
    var readyCallbacks = [];
    function emojioneReady (fn) {
        if (emojione) {
            fn();
        } else {
            readyCallbacks.push(fn);
        }
    };
    var blankImg = 'data:image/gif;base64,R0lGODlhAQABAJH/AP///wAAAMDAwAAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw==';
    var slice = [].slice;
    var css_class = "emojionearea";
    var emojioneSupportMode = 0;
    var invisibleChar = '&#8203;';
    function trigger(self, event, args) {
        var result = true, j = 1;
        if (event) {
            event = event.toLowerCase();
            do {
                var _event = j==1 ? '@' + event : event;
                if (eventStorage[self.id][_event] && eventStorage[self.id][_event].length) {
                    $.each(eventStorage[self.id][_event], function (i, fn) {
                        return result = fn.apply(self, args|| []) !== false;
                    });
                }
            } while (result && !!j--);
        }
        return result;
    }
    function attach(self, element, events, target) {
        target = target || function (event, callerEvent) { return $(callerEvent.currentTarget) };
        $.each(events, function(event, link) {
            event = $.isArray(events) ? link : event;
            (possibleEvents[self.id][link] || (possibleEvents[self.id][link] = []))
                .push([element, event, target]);
        });
    }
    function getTemplate(template, unicode, shortname) {
        var imageType = emojione.imageType, imagePath;
        if (imageType=='svg'){
            imagePath = emojione.imagePathSVG;
        } else {
            imagePath = emojione.imagePathPNG;
        }
        var friendlyName = '';
        if (shortname) {
            friendlyName = shortname.substr(1, shortname.length - 2).replace(/_/g, ' ').replace(/\w\S*/g, function(txt) { return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
        }
        var fname = '';
        if (unicode.uc_base && emojioneSupportMode > 4) {
			if ( unicode.uc_base.match( "^00" ) ) {
				fname = unicode.uc_base.replace( '00', '' );
			} else if ( shortname == ':chess_pawn:' || shortname == ':infinity:' ) {
				fname = unicode.uc_base;
			} else if ( shortname == ':eye_in_speech_bubble:' ) {
				fname = '1f441-200d-1f5e8';
			} else {
				fname = unicode.uc_output;
			}
            unicode = unicode.uc_output.toUpperCase();
        } else {
            fname = unicode;
        }
        template = template.replace('{name}', shortname || '')
            .replace('{friendlyName}', friendlyName)
            .replace('{img}', imagePath + (emojioneSupportMode < 2 ? fname.toUpperCase() : fname) + '.' + imageType)
            .replace('{uni}', unicode);

        if(shortname) {
            template = template.replace('{alt}', emojione.shortnameToUnicode(shortname));
        } else {
            template = template.replace('{alt}', emojione.convert(unicode));
        }

        return template;
    };
    function shortnameTo(str, template, clear) {
        return str.replace(/:?\+?[\w_\-]+:?/g, function(shortname) {
            shortname = ":" + shortname.replace(/:$/,'').replace(/^:/,'') + ":";
            var unicode = emojione.emojioneList[shortname];
            if (unicode) {
                if (emojioneSupportMode > 4) {
                    return getTemplate(template, unicode, shortname);
                } else {
                    if (emojioneSupportMode > 3) unicode = unicode.unicode;
                    return getTemplate(template, unicode[unicode.length-1], shortname);
                }
            }
            return clear ? '' : shortname;
        });
    };
    function pasteHtmlAtCaret(html) {
        var sel, range;
        if (window.getSelection) {
            sel = window.getSelection();
            if (sel.getRangeAt && sel.rangeCount) {
                range = sel.getRangeAt(0);
                range.deleteContents();
                var el = document.createElement("div");
                el.innerHTML = html;
                var frag = document.createDocumentFragment(), node, lastNode;
                while ( (node = el.firstChild) ) {
                    lastNode = frag.appendChild(node);
                }
                range.insertNode(frag);
                if (lastNode) {
                    range = range.cloneRange();
                    range.setStartAfter(lastNode);
                    range.collapse(true);
                    sel.removeAllRanges();
                    sel.addRange(range);
                }
            }
        } else if (document.selection && document.selection.type != "Control") {
            document.selection.createRange().pasteHTML(html);
        }
    }
	function placeCaretAtEnd(el) {
		el.focus();
		if (typeof window.getSelection != "undefined"
				&& typeof document.createRange != "undefined") {
			var range = document.createRange();
			range.selectNodeContents(el);
			range.collapse(false);
			var sel = window.getSelection();
			sel.removeAllRanges();
			sel.addRange(range);
		} else if (typeof document.body.createTextRange != "undefined") {
			var textRange = document.body.createTextRange();
			textRange.moveToElementText(el);
			textRange.collapse(false);
			textRange.select();
		}
	}
    function getEmojioneVersion() {
        return window.emojioneVersion || '4';
    };
    function isObject(variable) {
        return typeof variable === 'object';
    };
    function detectVersion(emojione) {
        var version;
        if (emojione.cacheBustParam) {
            version = emojione.cacheBustParam;
            if (!isObject(emojione['jsEscapeMap'])) return '1.5.2';
            if (version === "?v=1.2.4") return '2.0.0';
            if (version === "?v=2.0.1") return '2.1.0'; // v2.0.1 || v2.1.0
            if (version === "?v=2.1.1") return '2.1.1';
            if (version === "?v=2.1.2") return '2.1.2';
            if (version === "?v=2.1.3") return '2.1.3';
            if (version === "?v=2.1.4") return '2.1.4';
            if (version === "?v=2.2.7") return '2.2.7';
            return '2.2.7';
        } else {
            return emojione.emojiVersion;
        }
    };
    function getSupportMode(version) {
        switch (version) {
            case '1.5.2': return 0;
            case '2.0.0': return 1;
            case '2.1.0':
            case '2.1.1': return 2;
            case '2.1.2': return 3;
            case '2.1.3':
            case '2.1.4':
            case '2.2.7': return 4;
            case '3.0.1':
            case '3.0.2':
            case '3.0.3':
            case '3.0': return 5;
            case '3.1.0':
            case '3.1.1':
            case '3.1.2':
            case '3.1':
            default: return 6;
        }
    };
    function getDefaultOptions () {
        if ($.fn.emojioneArea && $.fn.emojioneArea.defaults) {
            return $.fn.emojioneArea.defaults;
        }

        var defaultOptions = {
            attributes: {
                dir               : "ltr",
                spellcheck        : false,
                autocomplete      : "off",
                autocorrect       : "off",
                autocapitalize    : "off",
            },
            search            : true,
            placeholder       : null,
            emojiPlaceholder  : ":smiley:",
            searchPlaceholder : "SEARCH",
            container         : null,
            hideSource        : true,
            shortnames        : true,
            sprite            : true,
            pickerPosition    : "top", // top | bottom | right
            filtersPosition   : "top", // top | bottom
            searchPosition    : "top", // top | bottom
            hidePickerOnBlur  : true,
            buttonTitle       : "Use the TAB key to insert emoji faster",
            tones             : true,
            tonesStyle        : "bullet", // bullet | radio | square | checkbox
            inline            : null, // null - auto
            saveEmojisAs      : "unicode", // unicode | shortname | image
            shortcuts         : true,
            autocomplete      : true,
            autocompleteTones : false,
            standalone        : false,
            useInternalCDN    : true, // Use the self loading mechanism
            imageType         : "svg", // Default image type used by internal CDN
            recentEmojis      : true,
            textcomplete: {
                maxCount      : 15,
                placement     : null // null - default | top | absleft | absright
            }
        };

        var supportMode = !emojione ? getSupportMode(getEmojioneVersion()) : getSupportMode(detectVersion(emojione));

        if (supportMode > 4) {
            defaultOptions.filters = {
                tones: {
                    title: "Diversity",
                    emoji: "open_hands raised_hands palms_up_together clap pray thumbsup thumbsdown punch fist left_facing_fist right_facing_fist " +
                    "fingers_crossed v metal love_you_gesture ok_hand point_left point_right point_up_2 point_down point_up raised_hand " +
                    "raised_back_of_hand hand_splayed vulcan wave call_me muscle middle_finger writing_hand selfie nail_care ear " +
                    "nose baby boy girl man woman blond-haired_woman blond-haired_man older_man older_woman " +
                    "man_with_chinese_cap woman_wearing_turban man_wearing_turban woman_police_officer " +
                    "man_police_officer woman_construction_worker man_construction_worker " +
                    "woman_guard man_guard woman_detective man_detective woman_health_worker man_health_worker " +
                    "woman_farmer man_farmer woman_cook man_cook woman_student man_student woman_singer man_singer woman_teacher " +
                    "man_teacher woman_factory_worker man_factory_worker woman_technologist man_technologist woman_office_worker " +
                    "man_office_worker woman_mechanic man_mechanic woman_scientist man_scientist woman_artist man_artist " +
                    "woman_firefighter man_firefighter woman_pilot man_pilot woman_astronaut man_astronaut woman_judge " +
                    "man_judge mrs_claus santa princess prince bride_with_veil man_in_tuxedo angel pregnant_woman " +
                    "breast_feeding woman_bowing man_bowing man_tipping_hand woman_tipping_hand " +
                    "man_gesturing_no woman_gesturing_no man_gesturing_ok woman_gesturing_ok " +
                    "man_raising_hand woman_raising_hand woman_facepalming man_facepalming " +
                    "woman_shrugging man_shrugging man_pouting woman_pouting " +
                    "man_frowning woman_frowning man_getting_haircut woman_getting_haircut " +
                    "man_getting_face_massage woman_getting_face_massage man_in_business_suit_levitating dancer man_dancing " +
                    "woman_walking man_walking woman_running man_running adult child older_adult " +
                    "bearded_person woman_with_headscarf woman_mage man_mage " +
                    "woman_fairy man_fairy woman_vampire man_vampire mermaid merman woman_elf man_elf " +
                    "snowboarder woman_lifting_weights man_lifting_weights woman_cartwheeling " +
                    "man_cartwheeling woman_bouncing_ball man_bouncing_ball " +
                    "woman_playing_handball man_playing_handball woman_golfing man_golfing " +
                    "woman_surfing man_surfing woman_swimming man_swimming woman_playing_water_polo " +
                    "man_playing_water_polo woman_rowing_boat man_rowing_boat " +
                    "horse_racing woman_biking man_biking woman_mountain_biking " +
                    "man_mountain_biking woman_juggling man_juggling " +
                    "woman_in_steamy_room man_in_steamy_room woman_climbing " +
                    "man_climbing woman_in_lotus_position man_in_lotus_position bath person_in_bed"
                },

                recent: {
                    icon: "clock2",
                    title: "Recent",
                    emoji: ""
                },

                smileys_people: {
                    icon: "grinning",
                    title: "Smileys & People",
                    emoji: "grinning smiley smile grin laughing sweat_smile joy rofl innocent wink blush slight_smile upside_down " +
                    "relaxed yum relieved heart_eyes smiling_face_with_3_hearts kissing_heart kissing kissing_smiling_eyes kissing_closed_eyes " +
                    "zany_face stuck_out_tongue_winking_eye stuck_out_tongue_closed_eyes stuck_out_tongue money_mouth sunglasses nerd face_with_monocle " +
                    "cowboy partying_face hugging clown smirk no_mouth neutral_face expressionless unamused rolling_eyes face_with_raised_eyebrow " +
					"thinking shushing_face face_with_hand_over_mouth lying_face flushed disappointed worried angry rage face_with_symbols_over_mouth pensive confused slight_frown " +
                    "frowning2 grimacing pleading_face persevere confounded tired_face weary triumph open_mouth scream fearful cold_sweat " +
                    "hushed frowning anguished cry disappointed_relieved sleepy drooling_face sweat sob " +
                    "star_struck dizzy_face woozy_face astonished exploding_head zipper_mouth mask head_bandage thermometer_face " +
                    "face_vomiting nauseated_face sneezing_face hot_face cold_face sleeping zzz " +
                    "smiling_imp imp japanese_ogre japanese_goblin poop ghost skull skull_crossbones alien " +
                    "robot jack_o_lantern smiley_cat smile_cat joy_cat heart_eyes_cat smirk_cat kissing_cat scream_cat crying_cat_face " +
                    "pouting_cat open_hands palms_up_together raised_hands clap pray handshake thumbsup thumbsdown punch fist left_facing_fist " +
                    "right_facing_fist fingers_crossed v metal love_you_gesture ok_hand point_left point_right point_up_2 point_down point_up " +
                    "raised_hand raised_back_of_hand hand_splayed vulcan wave call_me muscle middle_finger writing_hand selfie " +
                    "nail_care leg foot lips tooth tongue ear nose eye eyes brain bone bust_in_silhouette busts_in_silhouette speaking_head baby child " +
                    "boy girl adult man bearded_person blond_haired_person man_red_haired man_curly_haired man_bald man_white_haired woman " +
                    "blond-haired_woman woman_red_haired woman_curly_haired woman_bald woman_white_haired older_adult older_man older_woman man_with_chinese_cap " +
					"woman_wearing_turban man_wearing_turban woman_with_headscarf woman_police_officer police_officer woman_firefighter man_firefighter " +
					"woman_construction_worker construction_worker woman_factory_worker man_factory_worker woman_mechanic man_mechanic woman_farmer man_farmer " +
					"woman_cook man_cook woman_singer man_singer woman_artist man_artist woman_teacher man_teacher woman_student man_student woman_office_worker man_office_worker " +
					"woman_technologist man_technologist woman_scientist man_scientist woman_astronaut man_astronaut woman_health_worker man_health_worker woman_judge man_judge " +
					"woman_pilot man_pilot woman_guard guard woman_detective detective mrs_claus santa angel princess prince bride_with_veil man_in_tuxedo " +
					"man_in_business_suit_levitating woman_superhero man_superhero woman_supervillain man_supervillain woman_mage man_mage woman_elf man_elf " +
					"woman_fairy man_fairy woman_genie man_genie mermaid merman woman_vampire man_vampire woman_zombie man_zombie woman_bowing man_bowing " +
					"woman_tipping_hand man_tipping_hand woman_gesturing_no man_gesturing_no woman_gesturing_ok man_gesturing_ok woman_shrugging man_shrugging " +
					"woman_raising_hand man_raising_hand woman_facepalming man_facepalming woman_pouting man_pouting woman_frowning man_frowning woman_getting_haircut man_getting_haircut " +
					"woman_getting_face_massage man_getting_face_massage pregnant_woman breast_feeding woman_walking man_walking woman_running man_running " +
					"dancer man_dancing women_with_bunny_ears_partying men_with_bunny_ears_partying couple two_women_holding_hands two_men_holding_hands " +
					"couple_with_heart couple_ww couple_mm couplekiss kiss_ww kiss_mm family family_mwg family_mwgb family_mwbb family_mwgg family_wwb " +
					"family_wwg family_wwgb family_wwbb family_wwgg family_mmb family_mmg family_mmgb family_mmbb family_mmgg family_woman_boy family_woman_girl " +
					"family_woman_girl_boy family_woman_boy_boy family_woman_girl_girl family_man_boy family_man_girl family_man_girl_boy family_man_boy_boy family_man_girl_girl " +
					"womans_clothes shirt lab_coat coat jeans necktie dress kimono bikini lipstick kiss footprints socks high_heel sandal boot " +
                    "womans_flat_shoe mans_shoe athletic_shoe hiking_boot billed_cap womans_hat " +
                    "tophat mortar_board crown helmet_with_cross school_satchel pouch purse handbag briefcase eyeglasses dark_sunglasses goggles " +
                    "scarf gloves ring closed_umbrella umbrella2"
                },

                animals_nature: {
                    icon: "bear",
                    title: "Animals & Nature",
                    emoji: "dog cat mouse hamster rabbit bear teddy_bear panda_face koala tiger lion_face cow pig pig_nose frog monkey_face see_no_evil " +
                    "hear_no_evil speak_no_evil monkey gorilla chicken penguin bird baby_chick hatching_chick hatched_chick wolf fox raccoon boar horse zebra giraffe deer kangaroo " +
                    "unicorn bee bug butterfly snail beetle ant cricket spider spider_web scorpion mosquito microbe turtle snake lizard octopus squid lobster crab shrimp " +
                    "tropical_fish fish blowfish dolphin shark whale whale2 crocodile leopard tiger2 water_buffalo ox cow2 dromedary_camel camel llama " +
                    "elephant rhino hippopotamus goat ram sheep racehorse pig2 bat rooster turkey dove eagle duck swan owl peacock parrot dog2 poodle cat2 rabbit2 rat mouse2 chipmunk badger " +
                    "hedgehog feet dragon dragon_face sauropod t_rex cactus christmas_tree evergreen_tree deciduous_tree palm_tree seedling herb shamrock four_leaf_clover " +
                    "bamboo tanabata_tree leaves fallen_leaf maple_leaf ear_of_rice hibiscus sunflower rose wilted_rose tulip blossom cherry_blossom bouquet mushroom " +
                    "chestnut shell earth_americas earth_africa earth_asia full_moon waning_gibbous_moon last_quarter_moon " +
                    "waning_crescent_moon new_moon waxing_crescent_moon first_quarter_moon waxing_gibbous_moon crescent_moon new_moon_with_face " +
                    "full_moon_with_face first_quarter_moon_with_face last_quarter_moon_with_face star star2 dizzy sparkles comet sun_with_face " +
                    "sunny white_sun_small_cloud partly_sunny white_sun_cloud white_sun_rain_cloud cloud cloud_rain thunder_cloud_rain cloud_lightning zap fire boom " +
                    "snowflake cloud_snow snowman2 snowman wind_blowing_face dash cloud_tornado " +
                    "fog rainbow umbrella droplet sweat_drops ocean"
                },

                food_drink: {
                    icon: "hamburger",
                    title: "Food & Drink",
                    emoji: "green_apple apple pear tangerine lemon banana watermelon grapes strawberry melon cherries peach mango pineapple coconut " +
                    "kiwi tomato avocado eggplant hot_pepper cucumber leafy_green broccoli corn carrot salad potato sweet_potato peanuts honey_pot bread croissant " +
                    "french_bread pretzel bagel pancakes cheese poultry_leg meat_on_bone cut_of_meat fried_shrimp egg cooking bacon hamburger fries hotdog pizza " +
                    "spaghetti sandwich taco burrito stuffed_flatbread ramen shallow_pan_of_food stew canned_food salt fish_cake sushi bento curry " +
                    "rice_ball rice rice_cracker dumpling oden dango shaved_ice ice_cream icecream cake birthday cupcake pie custard lollipop candy " +
                    "chocolate_bar popcorn doughnut cookie fortune_cookie moon_cake coffee tea bowl_with_spoon baby_bottle cup_with_straw milk beer beers wine_glass champagne_glass " +
                    "tumbler_glass cocktail tropical_drink champagne sake spoon fork_and_knife fork_knife_plate " +
                    "chopsticks takeout_box"
                },

                activity: {
                    icon: "soccer",
                    title: "Activity",
                    emoji: "soccer basketball football baseball softball tennis volleyball rugby_football 8ball flying_disc ping_pong badminton goal hockey field_hockey " +
                    "cricket_game lacrosse curling_stone golf bow_and_arrow fishing_pole_and_fish boxing_glove martial_arts_uniform ice_skate ski sled skier snowboarder " +
                    "woman_lifting_weights man_lifting_weights person_fencing women_wrestling men_wrestling woman_cartwheeling " +
                    "man_cartwheeling woman_bouncing_ball man_bouncing_ball woman_playing_handball man_playing_handball woman_climbing man_climbing woman_golfing " +
                    "man_golfing woman_in_lotus_position man_in_lotus_position woman_in_steamy_room man_in_steamy_room  woman_surfing man_surfing woman_swimming man_swimming woman_playing_water_polo " +
                    "man_playing_water_polo woman_rowing_boat man_rowing_boat horse_racing woman_biking man_biking woman_mountain_biking man_mountain_biking " +
                    "running_shirt_with_sash military_medal medal first_place second_place " +
                    "third_place trophy rosette reminder_ribbon ticket tickets circus_tent woman_juggling man_juggling performing_arts art " +
                    "clapper microphone headphones musical_score musical_keyboard drum saxophone trumpet guitar violin game_die jigsaw chess_pawn dart bowling " +
                    "video_game space_invader slot_machine "
                },

                travel_places: {
                    icon: "oncoming_automobile",
                    title: "Travel & Places",
                    emoji: "red_car blue_car taxi bus trolleybus race_car police_car ambulance fire_engine minibus truck articulated_lorry tractor " +
                    "motorcycle motor_scooter bike scooter skateboard rotating_light oncoming_police_car oncoming_bus oncoming_automobile oncoming_taxi " +
                    "aerial_tramway mountain_cableway suspension_railway railway_car train monorail bullettrain_side " +
                    "bullettrain_front light_rail mountain_railway steam_locomotive train2 metro tram station helicopter airplane_small airplane " +
                    "airplane_departure airplane_arriving seat satellite_orbital rocket flying_saucer canoe sailboat motorboat speedboat " +
                    "ferry cruise_ship ship anchor fuelpump construction busstop vertical_traffic_light traffic_light octagonal_sign ferris_wheel roller_coaster " + 
					"carousel_horse construction_site foggy tokyo_tower factory fountain rice_scene mountain mountain_snow mount_fuji volcano " +
					"japan camping tent park motorway railway_track sunrise sunrise_over_mountains desert beach island " +
					"city_sunset city_dusk cityscape night_with_stars bridge_at_night milky_way stars sparkler fireworks homes european_castle japanese_castle " +
					"stadium statue_of_liberty " +
                    "house house_with_garden house_abandoned office department_store post_office european_post_office hospital " +
                    "bank hotel convenience_store school love_hotel wedding classical_building church mosque synagogue kaaba shinto_shrine"
                },

                objects: {
                    icon: "bulb",
                    title: "Objects",
                    emoji: "watch iphone calling computer keyboard desktop printer mouse_three_button trackball joystick compression minidisc " +
                    "floppy_disk cd dvd vhs camera camera_with_flash video_camera movie_camera projector film_frames telephone_receiver " +
                    "telephone pager fax tv radio microphone2 level_slider control_knobs stopwatch timer alarm_clock clock hourglass_flowing_sand " +
                    "hourglass abacus satellite battery electric_plug bulb flashlight candle fire_extinguisher wastebasket oil shopping_cart money_with_wings " +
                    "dollar yen euro pound moneybag credit_card receipt gem scales toolbox wrench hammer hammer_pick tools pick nut_and_bolt gear " +
                    "chains bricks gun firecracker bomb knife dagger crossed_swords shield smoking coffin urn amphora crystal_ball prayer_beads nazar_amulet barber magnet " +
                    "alembic test_tube petri_dish dna telescope microscope hole pill syringe thermometer label bookmark toilet shower bathtub bath squeeze_bottle roll_of_paper soap " +
					"sponge broom basket key " +
                    "key2 couch sleeping_accommodation bed door luggage bellhop frame_photo compass map beach_umbrella moyai shopping_bags balloon flags ribbon " +
                    "red_envelope gift confetti_ball tada dolls wind_chime izakaya_lantern envelope envelope_with_arrow incoming_envelope e-mail " +
                    "love_letter postbox mailbox_closed mailbox mailbox_with_mail mailbox_with_no_mail package postal_horn inbox_tray outbox_tray " +
                    "scroll page_with_curl bookmark_tabs bar_chart chart_with_upwards_trend " +
                    "chart_with_downwards_trend page_facing_up date calendar calendar_spiral card_index card_box ballot_box " +
                    "file_cabinet clipboard notepad_spiral file_folder open_file_folder dividers newspaper2 newspaper notebook " +
                    "closed_book green_book blue_book orange_book notebook_with_decorative_cover ledger books book link " +
                    "paperclip paperclips scissors triangular_ruler straight_ruler pushpin round_pushpin safety_pin thread yarn closed_lock_with_key lock unlock lock_with_ink_pen " +
					"pen_ballpoint pen_fountain black_nib pencil pencil2 crayon paintbrush mag mag_right"
                },

                symbols: {
                    icon: "symbols",
                    title: "Symbols",
                    emoji: "heart orange_heart yellow_heart green_heart blue_heart purple_heart black_heart broken_heart heart_exclamation two_hearts " +
                    "revolving_hearts heartbeat heartpulse sparkling_heart cupid gift_heart heart_decoration peace cross star_and_crescent " +
                    "om_symbol wheel_of_dharma star_of_david six_pointed_star menorah yin_yang orthodox_cross place_of_worship ophiuchus " +
                    "aries taurus gemini cancer leo virgo libra scorpius sagittarius capricorn aquarius pisces id atom medical_symbol radioactive " +
                    "biohazard mobile_phone_off vibration_mode u6709 u7121 u7533 u55b6 u6708 eight_pointed_black_star vs accept white_flower " +
                    "ideograph_advantage secret congratulations u5408 u6e80 u5272 u7981 a b ab cl o2 sos no_entry name_badge no_entry_sign x o " +
                    "anger hotsprings no_pedestrians do_not_litter no_bicycles non-potable_water underage " +
                    "no_mobile_phones no_smoking exclamation grey_exclamation question grey_question bangbang interrobang 100 low_brightness " +
                    "high_brightness trident fleur-de-lis part_alternation_mark warning children_crossing beginner recycle " +
                    "u6307 chart sparkle eight_spoked_asterisk negative_squared_cross_mark white_check_mark " +
                    "diamond_shape_with_a_dot_inside cyclone loop globe_with_meridians infinity atm wc wheelchair parking u7a7a sa passport_control customs " +
                    "baggage_claim left_luggage potable_water mens male_sign womens female_sign baby_symbol restroom put_litter_in_its_place cinema signal_strength koko ng " +
                    "ok up cool new free zero one two three four five six seven " +
                    "eight nine keycap_ten 1234 arrow_forward pause_button play_pause stop_button record_button eject " +
                    "track_next track_previous fast_forward rewind twisted_rightwards_arrows repeat repeat_one arrow_backward arrow_up_small arrow_down_small " +
					"arrow_double_up arrow_double_down " +
                    "arrow_right arrow_left arrow_up arrow_down arrow_upper_right arrow_lower_right arrow_lower_left " +
                    "arrow_upper_left arrow_up_down left_right_arrow arrows_counterclockwise arrow_right_hook leftwards_arrow_with_hook arrows_clockwise arrow_heading_up " +
                    "arrow_heading_down hash asterisk information_source abc abcd capital_abcd symbols " +
                    "musical_note notes wavy_dash curly_loop heavy_check_mark heavy_plus_sign heavy_minus_sign heavy_division_sign heavy_multiplication_x heavy_dollar_sign " +
                    "currency_exchange end back on top soon " +
                    "ballot_box_with_check radio_button red_circle blue_circle black_circle white_circle black_large_square white_large_square " +
					"black_medium_square white_medium_square black_medium_small_square white_medium_small_square black_small_square white_small_square " +
                    "small_orange_diamond small_blue_diamond large_orange_diamond large_blue_diamond small_red_triangle small_red_triangle_down " +
                    "black_square_button white_square_button speaker " +
                    "sound loud_sound mute mega loudspeaker bell no_bell black_joker mahjong spades clubs hearts diamonds flower_playing_cards eye_in_speech_bubble " +
					"speech_left thought_balloon anger_right speech_balloon clock1 clock2 clock3 clock4 clock5 " +
                    "clock6 clock7 clock8 clock9 clock10 clock11 clock12 clock130 clock230 clock330 clock430 clock530 clock630 " +
                    "clock730 clock830 clock930 clock1030 clock1130 clock1230"
                },

                flags: {
                    icon: "triangular_flag_on_post",
                    title: "Flags",
                    emoji: "flag_white flag_black checkered_flag triangular_flag_on_post crossed_flags pirate_flag rainbow_flag flag_ac flag_ad flag_ae " +
					"flag_af flag_ag flag_ai flag_al flag_am flag_ao flag_aq flag_ar flag_as flag_at flag_au flag_aw flag_ax flag_az flag_ba flag_bb flag_bd " +
					"flag_be flag_bf flag_bg flag_bh flag_bi flag_bj flag_bl flag_bm flag_bn flag_bo flag_bq flag_br flag_bs flag_bt flag_bw flag_by " +
					"flag_bz flag_ca flag_cc flag_cd flag_cf flag_cg flag_ch flag_ci flag_ck flag_cl flag_cm flag_cn flag_co flag_cr flag_cu flag_cv flag_cw flag_cx " +
					"flag_cy flag_cz flag_de flag_dj flag_dk flag_dm flag_do flag_dz flag_ec england flag_ee flag_eg flag_eh flag_er flag_es flag_et flag_eu flag_fi " +
					"flag_fj flag_fk flag_fm flag_fo flag_fr flag_ga flag_gb flag_gd flag_ge flag_gf flag_gg flag_gh flag_gi flag_gl flag_gm flag_gn flag_gp flag_gq " +
					"flag_gr flag_gs flag_gt flag_gu flag_gw flag_gy flag_hk flag_hn flag_hr flag_ht flag_hu flag_ic flag_id flag_ie flag_il flag_im flag_in flag_io " +
					"flag_iq flag_ir flag_is flag_it flag_je flag_jm flag_jo flag_jp flag_ke flag_kg flag_kh flag_ki flag_km flag_kn flag_kp flag_kr flag_kw flag_ky " +
					"flag_kz flag_la flag_lb flag_lc flag_li flag_lk flag_lr flag_ls flag_lt flag_lu flag_lv flag_ly flag_ma flag_mc flag_md flag_me flag_mg flag_mh " +
					"flag_mk flag_ml flag_mm flag_mn flag_mo flag_mp flag_mq flag_mr flag_ms flag_mt flag_mu flag_mv flag_mw flag_mx flag_my flag_mz flag_na flag_nc " +
					"flag_ne flag_nf flag_ng flag_ni flag_nl flag_no flag_np flag_nr flag_nu flag_nz flag_om flag_pa flag_pe flag_pf flag_pg flag_ph flag_pk flag_pl " +
					"flag_pm flag_pn flag_pr flag_ps flag_pt flag_pw flag_py flag_qa flag_re flag_ro flag_rs flag_ru flag_rw flag_sa scotland flag_sb flag_sc flag_sd flag_se " +
					"flag_sg flag_sh flag_si flag_sk flag_sl flag_sm flag_sn flag_so flag_sr flag_ss flag_st flag_sv flag_sx flag_sy flag_sz flag_ta flag_tc flag_td flag_tf " +
					"flag_tg flag_th flag_tj flag_tk flag_tl flag_tm flag_tn flag_to flag_tr flag_tt flag_tv flag_tw flag_tz flag_ua flag_ug united_nations flag_us flag_uy " +
					"flag_uz flag_va flag_vc flag_ve flag_vg flag_vi flag_vn flag_vu wales flag_wf flag_ws flag_xk flag_ye flag_yt flag_za flag_zm flag_zw"
                 }
            };
        };

        return defaultOptions;
    };
    function getOptions(options) {
        var default_options = getDefaultOptions();
        if (options && options['filters']) {
            var filters = default_options.filters;
            $.each(options['filters'], function(filter, data) {
                if (!isObject(data) || $.isEmptyObject(data)) {
                    delete filters[filter];
                    return;
                }
                $.each(data, function(key, val) {
                    filters[filter][key] = val;
                });
            });
            options['filters'] = filters;
        }
        return $.extend({}, default_options, options);
    };

    var saveSelection, restoreSelection;
    if (window.getSelection && document.createRange) {
        saveSelection = function(el) {
            var sel = window.getSelection && window.getSelection();
            if (sel && sel.rangeCount > 0) {
                return sel.getRangeAt(0);
            }
        };

        restoreSelection = function(el, sel) {
            var range = document.createRange();
            range.setStart(sel.startContainer, sel.startOffset);
            range.setEnd(sel.endContainer, sel.endOffset)

            sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
        }
    } else if (document.selection && document.body.createTextRange) {
        saveSelection = function(el) {
            return document.selection.createRange();
        };

        restoreSelection = function(el, sel) {
            var textRange = document.body.createTextRange();
            textRange.moveToElementText(el);
            textRange.setStart(sel.startContanier, sel.startOffset);
            textRange.setEnd(sel.endContainer, sel.endOffset);
            textRange.select();
        };
    }


    var uniRegexp;
    function unicodeTo(str, template) {
        return str.replace(uniRegexp, function(unicodeChar) {
            var map = emojione[(emojioneSupportMode === 0 ? 'jsecapeMap' : 'jsEscapeMap')];
            if (typeof unicodeChar !== 'undefined' && unicodeChar in map) {
                return getTemplate(template, map[unicodeChar], emojione.toShort(unicodeChar));
            }
            return unicodeChar;
        });
    }
    function htmlFromText(str, self) {
        str = str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#x27;')
            .replace(/`/g, '&#x60;')
            .replace(/(?:\r\n|\r|\n)/g, '\n')
            .replace(/(\n+)/g, '<div>$1</div>')
            .replace(/\n/g, '<br/>')
            .replace(/<br\/><\/div>/g, '</div>');
        if (self.shortnames) {
            str = emojione.shortnameToUnicode(str);
        }
        return unicodeTo(str, self.emojiTemplate)
            .replace(/\t/g, '&nbsp;&nbsp;&nbsp;&nbsp;')
            .replace(/  /g, '&nbsp;&nbsp;');
    }
    function textFromHtml(str, self) {
        str = str
            .replace(/&#10;/g, '\n')
            .replace(/&#09;/g, '\t')
            .replace(/<img[^>]*alt="([^"]+)"[^>]*>/ig, '$1')
            .replace(/\n|\r/g, '')
            .replace(/<br[^>]*>/ig, '\n')
            .replace(/(?:<(?:div|p|ol|ul|li|pre|code|object)[^>]*>)+/ig, '<div>')
            .replace(/(?:<\/(?:div|p|ol|ul|li|pre|code|object)>)+/ig, '</div>')
            .replace(/\n<div><\/div>/ig, '\n')
            .replace(/<div><\/div>\n/ig, '\n')
            .replace(/(?:<div>)+<\/div>/ig, '\n')
            .replace(/([^\n])<\/div><div>/ig, '$1\n')
            .replace(/(?:<\/div>)+/ig, '</div>')
            .replace(/([^\n])<\/div>([^\n])/ig, '$1\n$2')
            .replace(/<\/div>/ig, '')
            .replace(/([^\n])<div>/ig, '$1\n')
            .replace(/\n<div>/ig, '\n')
            .replace(/<div>\n/ig, '\n\n')
            .replace(/<(?:[^>]+)?>/g, '')
            .replace(new RegExp(invisibleChar, 'g'), '')
            .replace(/&nbsp;/g, ' ')
            .replace(/&lt;/g, '<')
            .replace(/&gt;/g, '>')
            .replace(/&quot;/g, '"')
            .replace(/&#x27;/g, "'")
            .replace(/&#x60;/g, '`')
            .replace(/&#60;/g, '<')
            .replace(/&#62;/g, '>')
            .replace(/&amp;/g, '&');

        switch (self.saveEmojisAs) {
            case 'image':
                str = unicodeTo(str, self.emojiTemplate);
                break;
            case 'shortname':
                str = emojione.toShort(str);
        }
        return str;
    }
    function calcButtonPosition() {
        var self = this,
            offset = self.editor[0].offsetWidth - self.editor[0].clientWidth,
            current = parseInt(self.button.css('marginRight'));
        if (current !== offset) {
            self.button.css({marginRight: offset});
            if (self.floatingPicker) {
                self.picker.css({right: parseInt(self.picker.css('right')) - current + offset});
            }
        }
    }
    function lazyLoading() {
        var self = this;
        if (!self.sprite && self.lasyEmoji[0] && self.lasyEmoji.eq(0).is(".lazy-emoji")) {
            var pickerTop = self.picker.offset().top,
                pickerBottom = pickerTop + self.picker.height() + 20;

            self.lasyEmoji.each(function() {
                var e = $(this), top = e.offset().top;

                if (top > pickerTop && top < pickerBottom) {
                    e.attr("src", e.data("src")).removeClass("lazy-emoji");
                }

                if (top > pickerBottom) {
                    return false;
                }
            });
            self.lasyEmoji = self.lasyEmoji.filter(".lazy-emoji");
        }
    };
    function selector (prefix, skip_dot) {
        return (skip_dot ? '' : '.') + css_class + (prefix ? ("-" + prefix) : "");
    }
    function div(prefix) {
        var parent = $('<div/>', isObject(prefix) ? prefix : {"class" : selector(prefix, true)});
        $.each(slice.call(arguments).slice(1), function(i, child) {
            if ($.isFunction(child)) {
                child = child.call(parent);
            }
            if (child) {
                $(child).appendTo(parent);
            }
        });
        return parent;
    }
    function getRecent () {
        return localStorage.getItem("recent_emojis") || "";
    }
    function updateRecent(self, show) {
        var emojis = getRecent();
        if (!self.recent || self.recent !== emojis || show) {
            if (emojis.length) {
                var skinnable = self.scrollArea.is(".skinnable"),
                    scrollTop, height;

                if (!skinnable) {
                    scrollTop = self.scrollArea.scrollTop();
                    if (show) {
                        self.recentCategory.show();
                    }
                    height = self.recentCategory.is(":visible") ? self.recentCategory.height() : 0;
                }

                var items = shortnameTo(emojis, self.emojiBtnTemplate, true).split('|').join('');
                self.recentCategory.children(".emojibtn").remove();
                $(items).insertAfter(self.recentCategory.children(".emojionearea-category-title"));


                self.recentCategory.children(".emojibtn").on("click", function() {
                    self.trigger("emojibtn.click", $(this));
                });

                self.recentFilter.show();

                if (!skinnable) {
                    self.recentCategory.show();

                    var height2 = self.recentCategory.height();

                    if (height !== height2) {
                        self.scrollArea.scrollTop(scrollTop + height2 - height);
                    }
                }
            } else {
                if (self.recentFilter.hasClass("active")) {
                    self.recentFilter.removeClass("active").next().addClass("active");
                }
                self.recentCategory.hide();
                self.recentFilter.hide();
            }
            self.recent = emojis;
        }
    };
    function setRecent(self, emoji) {
        var recent = getRecent();
        var emojis = recent.split("|");

        var index = emojis.indexOf(emoji);
        if (index !== -1) {
            emojis.splice(index, 1);
        }
        emojis.unshift(emoji);

        if (emojis.length > 9) {
            emojis.pop();
        }

        localStorage.setItem("recent_emojis", emojis.join("|"));

        updateRecent(self);
    };
// see https://github.com/Modernizr/Modernizr/blob/master/feature-detects/storage/localstorage.js
    function supportsLocalStorage () {
        var test = 'test';
        try {
            localStorage.setItem(test, test);
            localStorage.removeItem(test);
            return true;
        } catch(e) {
            return false;
        }
    }
    function init(self, source, options) {
        //calcElapsedTime('init', function() {
        self.options = options = getOptions(options);
        self.sprite = options.sprite && emojioneSupportMode < 3;
        self.inline = options.inline === null ? source.is("INPUT") : options.inline;
        self.shortnames = options.shortnames;
        self.saveEmojisAs = options.saveEmojisAs;
        self.standalone = options.standalone;
        self.emojiTemplate = '<img alt="{alt}" class="emojione' + (self.sprite ? '-{uni}" src="' + blankImg + '"/>' : 'emoji" src="{img}"/>');
        self.emojiTemplateAlt = self.sprite ? '<i class="emojione-{uni}"/>' : '<img class="emojioneemoji" src="{img}"/>';
        self.emojiBtnTemplate = '<i class="emojibtn" role="button" data-name="{name}" title="{friendlyName}">' + self.emojiTemplateAlt + '</i>';
        self.recentEmojis = options.recentEmojis && supportsLocalStorage();

        var pickerPosition = options.pickerPosition;
        self.floatingPicker = pickerPosition === 'top' || pickerPosition === 'bottom';
        self.source = source;

        if (source.is(":disabled") || source.is(".disabled")) {
            self.disable();
        }

        var sourceValFunc = source.is("TEXTAREA") || source.is("INPUT") ? "val" : "text",
            editor, button, picker, filters, filtersBtns, searchPanel, emojisList, categories, categoryBlocks, scrollArea,
            tones = div('tones',
                options.tones ?
                    function() {
                        this.addClass(selector('tones-' + options.tonesStyle, true));
                        for (var i = 0; i <= 5; i++) {
                            this.append($("<i/>", {
                                "class": "btn-tone btn-tone-" + i + (!i ? " active" : ""),
                                "data-skin": i,
                                role: "button"
                            }));
                        }
                    } : null
            ),
            app = div({
                "class" : css_class + ((self.standalone) ? " " + css_class + "-standalone " : " ") + (source.attr("class") || ""),
                role: "application"
            },
            editor = self.editor = div("editor").attr({
                contenteditable: (self.standalone) ? false : true,
                placeholder: options.placeholder || source.data("placeholder") || source.attr("placeholder") || "",
                tabindex: 0
            }),
            button = self.button = div('button',
                div('button-open'),
                div('button-close')
            ).attr('title', options.buttonTitle),
            picker = self.picker = div('picker',
                div('wrapper',
                    filters = div('filters'),
                    (options.search ?
                        searchPanel = div('search-panel',
                            div('search',
                                options.search ?
                                function() {
                                    self.search = $("<input/>", {
                                        "placeholder": options.searchPlaceholder || "",
                                        "type": "text",
                                        "class": "search"
                                    });
                                    this.append(self.search);
                                } : null
                            ),
                            tones
                        ) : null
                    ),
                    scrollArea = div('scroll-area',
                        options.tones && !options.search ? div('tones-panel',
                            tones
                        ) : null,
                        emojisList = div('emojis-list')
                    )
                )
            ).addClass(selector('picker-position-' + options.pickerPosition, true))
             .addClass(selector('filters-position-' + options.filtersPosition, true))
             .addClass(selector('search-position-' + options.searchPosition, true))
             .addClass('hidden')
        );

        if (options.search) {
            searchPanel.addClass(selector('with-search', true));
        }

        self.searchSel = null;

        editor.data(source.data());

        $.each(options.attributes, function(attr, value) {
            editor.attr(attr, value);
        });

        var mainBlock = div('category-block').attr({"data-tone": 0}).prependTo(emojisList);

        $.each(options.filters, function(filter, params) {
            var skin = 0;
            if (filter === 'recent' && !self.recentEmojis) {
                return;
            }
            if (filter !== 'tones') {
                $("<i/>", {
                    "class": selector("filter", true) + " " + selector("filter-" + filter, true),
                    "data-filter": filter,
                    title: params.title
                })
                .wrapInner(shortnameTo(params.icon, self.emojiTemplateAlt))
                .appendTo(filters);
            } else if (options.tones) {
                skin = 5;
            } else {
                return;
            }

            do {
                var category,
                    items = params.emoji.replace(/[\s,;]+/g, '|');

                if (skin === 0) {
                    category = div('category').attr({
                        name: filter,
                        "data-tone": skin
                    }).appendTo(mainBlock);
                } else {
                    category = div('category-block').attr({
                        name: filter,
                        "data-tone": skin
                    }).appendTo(emojisList);
                }

                if (skin > 0) {
                    category.hide();
                    items = items.split('|').join('_tone' + skin + '|') + '_tone' + skin;
                }

                if (filter === 'recent') {
                    items = getRecent();
                }

                items = shortnameTo(items,
                    self.sprite ?
                        '<i class="emojibtn" role="button" data-name="{name}" title="{friendlyName}"><i class="emojione-{uni}"></i></i>' :
                        '<i class="emojibtn" role="button" data-name="{name}" title="{friendlyName}"><img class="emojioneemoji lazy-emoji" data-src="{img}"/></i>',
                    true).split('|').join('');

                category.html(items);
                $('<div class="emojionearea-category-title"/>').text(params.title).prependTo(category);
            } while (--skin > 0);
        });

        options.filters = null;
        if (!self.sprite) {
            self.lasyEmoji = emojisList.find(".lazy-emoji");
        }

        filtersBtns = filters.find(selector("filter"));
        filtersBtns.eq(0).addClass("active");
        categoryBlocks = emojisList.find(selector("category-block"))
        categories = emojisList.find(selector("category"))

        self.recentFilter = filtersBtns.filter('[data-filter="recent"]');
        self.recentCategory = categories.filter("[name=recent]");

        self.scrollArea = scrollArea;

        if (options.container) {
            $(options.container).wrapInner(app);
        } else {
            app.insertAfter(source);
        }

        if (options.hideSource) {
            source.hide();
        }

        self.setText(source[sourceValFunc]());
        source[sourceValFunc](self.getText());
        calcButtonPosition.apply(self);

        // if in standalone mode and no value is set, initialise with a placeholder
        if (self.standalone && !self.getText().length) {
            var placeholder = $(source).data("emoji-placeholder") || options.emojiPlaceholder;
            self.setText(placeholder);
            editor.addClass("has-placeholder");
        }

        // attach() must be called before any .on() methods !!!
        // 1) attach() stores events into possibleEvents{},
        // 2) .on() calls bindEvent() and stores handlers into eventStorage{},
        // 3) bindEvent() finds events in possibleEvents{} and bind founded via jQuery.on()
        // 4) attached events via jQuery.on() calls trigger()
        // 5) trigger() calls handlers stored into eventStorage{}

        attach(self, emojisList.find(".emojibtn"), {click: "emojibtn.click"});
        attach(self, window, {resize: "!resize"});
        attach(self, tones.children(), {click: "tone.click"});
        attach(self, [picker, button], {mousedown: "!mousedown"}, editor);
        attach(self, button, {click: "button.click"});
        attach(self, editor, {paste :"!paste"}, editor);
        attach(self, editor, ["focus", "blur"], function() { return self.stayFocused ? false : editor; } );
        attach(self, picker, {mousedown: "picker.mousedown", mouseup: "picker.mouseup", click: "picker.click",
            keyup: "picker.keyup", keydown: "picker.keydown", keypress: "picker.keypress"});
        attach(self, editor, ["mousedown", "mouseup", "click", "keyup", "keydown", "keypress"]);
        attach(self, picker.find(".emojionearea-filter"), {click: "filter.click"});
        attach(self, source, {change: "source.change"});

        if (options.search) {
            attach(self, self.search, {keyup: "search.keypress", focus: "search.focus", blur: "search.blur"});
        }

        var noListenScroll = false;
        scrollArea.on('scroll', function () {
            if (!noListenScroll) {
                lazyLoading.call(self);
                if (scrollArea.is(":not(.skinnable)")) {
                    var item = categories.eq(0), scrollTop = scrollArea.offset().top;
                    categories.each(function (i, e) {
                        if ($(e).offset().top - scrollTop >= 10) {
                            return false;
                        }
                        item = $(e);
                    });
                    var filter = filtersBtns.filter('[data-filter="' + item.attr("name") + '"]');
                    if (filter[0] && !filter.is(".active")) {
                        filtersBtns.removeClass("active");
                        filter.addClass("active");
                    }
                }
            }
        });

        self.on("@filter.click", function(filter) {
            var isActive = filter.is(".active");
            if (scrollArea.is(".skinnable")) {
                if (isActive) return;
                tones.children().eq(0).click();
            }
            noListenScroll = true;
            if (!isActive) {
                filtersBtns.filter(".active").removeClass("active");
                filter.addClass("active");
            }
            var headerOffset = categories.filter('[name="' + filter.data('filter') + '"]').offset().top,
                scroll = scrollArea.scrollTop(),
                offsetTop = scrollArea.offset().top;

            scrollArea.stop().animate({
                scrollTop: headerOffset + scroll - offsetTop - 2
            }, 200, 'swing', function () {
                lazyLoading.call(self);
                noListenScroll = false;
            });
        })

        .on("@picker.show", function() {
            if (self.recentEmojis) {
                updateRecent(self);
            }
            lazyLoading.call(self);
        })

        .on("@tone.click", function(tone) {
            tones.children().removeClass("active");
            var skin = tone.addClass("active").data("skin");
            if (skin) {
                scrollArea.addClass("skinnable");
                categoryBlocks.hide().filter("[data-tone=" + skin + "]").show();
                filtersBtns.removeClass("active");//.not('[data-filter="recent"]').eq(0).addClass("active");
            } else {
                scrollArea.removeClass("skinnable");
                categoryBlocks.hide().filter("[data-tone=0]").show();
                filtersBtns.eq(0).click();
            }
            lazyLoading.call(self);
            if (options.search) {
                self.trigger('search.keypress');
            }
        })

        .on("@button.click", function(button) {
            if (button.is(".active")) {
                self.hidePicker();
            } else {
                self.showPicker();
                self.searchSel = null;
            }
        })

        .on("@!paste", function(editor, event) {

            var pasteText = function(text) {
                var caretID = "caret-" + (new Date()).getTime();
                var html = htmlFromText(text, self);
                pasteHtmlAtCaret(html);
                pasteHtmlAtCaret('<i id="' + caretID +'"></i>');
                editor.scrollTop(editorScrollTop);
                var caret = $("#" + caretID),
                    top = caret.offset().top - editor.offset().top,
                    height = editor.height();
                if (editorScrollTop + top >= height || editorScrollTop > top) {
                    editor.scrollTop(editorScrollTop + top - 2 * height/3);
                }
                caret.remove();
                self.stayFocused = false;
                calcButtonPosition.apply(self);
                trigger(self, 'paste', [editor, text, html]);
            };

            if (event.originalEvent.clipboardData) {
                var text = event.originalEvent.clipboardData.getData('text/plain');
                pasteText(text);

                if (event.preventDefault){
                    event.preventDefault();
                } else {
                    event.stop();
                }

                event.returnValue = false;
                event.stopPropagation();
                return false;
            }

            self.stayFocused = true;
            // insert invisible character for fix caret position
            pasteHtmlAtCaret('<span>' + invisibleChar + '</span>');

            var sel = saveSelection(editor[0]),
                editorScrollTop = editor.scrollTop(),
                clipboard = $("<div/>", {contenteditable: true})
                    .css({position: "fixed", left: "-999px", width: "1px", height: "1px", top: "20px", overflow: "hidden"})
                    .appendTo($("BODY"))
                    .focus();

            window.setTimeout(function() {
                editor.focus();
                restoreSelection(editor[0], sel);
                var text = textFromHtml(clipboard.html().replace(/\r\n|\n|\r/g, '<br>'), self);
                clipboard.remove();
                pasteText(text);
            }, 200);
        })

        .on("@emojibtn.click", function(emojibtn) {
            editor.removeClass("has-placeholder");

            if (self.searchSel !== null) {
                restoreSelection(editor[0], self.searchSel);
                self.searchSel = null;
            }

            if (self.standalone) {
                editor.html(shortnameTo(emojibtn.data("name"), self.emojiTemplate));
                self.trigger("blur");
            } else {
				placeCaretAtEnd( editor.get(0) );
                saveSelection(editor[0]);
                pasteHtmlAtCaret(shortnameTo(emojibtn.data("name"), self.emojiTemplate));
            }

            if (self.recentEmojis) {
                setRecent(self, emojibtn.data("name"));
            }

            // self.search.val('').trigger("change");
            self.trigger('search.keypress');
        })

        .on("@!resize @keyup @emojibtn.click", calcButtonPosition)

        .on("@!mousedown", function(editor, event) {
            if ($(event.target).hasClass('search')) {
                // Allow search clicks
                self.stayFocused = true;
                if (self.searchSel === null) {
                    self.searchSel = saveSelection(editor[0]);
                }
            } else {
                if (!app.is(".focused")) {
                    editor.trigger("focus");
                }
                event.preventDefault();
            }
            return false;
        })

        .on("@change", function() {
            var html = self.editor.html().replace(/<\/?(?:div|span|p)[^>]*>/ig, '');
            // clear input: chrome adds <br> when contenteditable is empty
            if (!html.length || /^<br[^>]*>$/i.test(html)) {
                self.editor.html(self.content = '');
            }
            source[sourceValFunc](self.getText());
        })

        .on("@source.change", function() {
            self.setText(source[sourceValFunc]());
            trigger('change');
        })

        .on("@focus", function() {
            app.addClass("focused");
        })

        .on("@blur", function() {
            app.removeClass("focused");

            if (options.hidePickerOnBlur) {
                self.hidePicker();
            }

            var content = self.editor.html();
            if (self.content !== content) {
                self.content = content;
                trigger(self, 'change', [self.editor]);
                source.trigger("blur").trigger("change");
            } else {
                source.trigger("blur");
            }

            if (options.search) {
                self.search.val('');
                // self.trigger('search.keypress', true);
            }
        });

        if (options.search) {
            self.on("@search.focus", function() {
                self.stayFocused = true;
                self.search.addClass("focused");
            })

            .on("@search.keypress", function(hide) {
                var filterBtns = picker.find(".emojionearea-filter");
                var activeTone = (options.tones ? tones.find("i.active").data("skin") : 0);
                var term = self.search.val().replace( / /g, "_" ).replace(/"/g, "\\\"");

                if (term && term.length) {
                    if (self.recentFilter.hasClass("active")) {
                        self.recentFilter.removeClass("active").next().addClass("active");
                    }

                    self.recentCategory.hide();
                    self.recentFilter.hide();

                    categoryBlocks.each(function() {
                        var matchEmojis = function(category, activeTone) {
                            var $matched = category.find('.emojibtn[data-name*="' + term + '"]');
                            if ($matched.length === 0) {
                                if (category.data('tone') === activeTone) {
                                    category.hide();
                                }
                                filterBtns.filter('[data-filter="' + category.attr('name') + '"]').hide();
                            } else {
                                var $notMatched = category.find('.emojibtn:not([data-name*="' + term + '"])');
                                $notMatched.hide();

                                $matched.show();

                                if (category.data('tone') === activeTone) {
                                    category.show();
                                }

                                filterBtns.filter('[data-filter="' + category.attr('name') + '"]').show();
                            }
                        }

                        var $block = $(this);
                        if ($block.data('tone') === 0) {
                            categories.filter(':not([name="recent"])').each(function() {
                                matchEmojis($(this), 0);
                            })
                        } else {
                            matchEmojis($block, activeTone);
                        }
                    });
                    if (!noListenScroll) {
                        scrollArea.trigger('scroll');
                    } else {
                        lazyLoading.call(self);
                    }
                } else {
                    updateRecent(self, true);
                    categoryBlocks.filter('[data-tone="' + tones.find("i.active").data("skin") + '"]:not([name="recent"])').show();
                    $('.emojibtn', categoryBlocks).show();
                    filterBtns.show();
                    lazyLoading.call(self);
                }
            })

            .on("@search.blur", function() {
                self.stayFocused = false;
                self.search.removeClass("focused");
                self.trigger("blur");
            });
        }

        if (options.shortcuts) {
            self.on("@keydown", function(_, e) {
                if (!e.ctrlKey) {
                    if (e.which == 9) {
                        e.preventDefault();
                        button.click();
                    }
                    else if (e.which == 27) {
                        e.preventDefault();
                        if (button.is(".active")) {
                            self.hidePicker();
                        }
                    }
                }
            });
        }

        if (isObject(options.events) && !$.isEmptyObject(options.events)) {
            $.each(options.events, function(event, handler) {
                self.on(event.replace(/_/g, '.'), handler);
            });
        }

        if (options.autocomplete) {
            var autocomplete = function() {
                var textcompleteOptions = {
                    maxCount: options.textcomplete.maxCount,
                    placement: options.textcomplete.placement
                };

                if (options.shortcuts) {
                    textcompleteOptions.onKeydown = function (e, commands) {
                        if (!e.ctrlKey && e.which == 13) {
                            return commands.KEY_ENTER;
                        }
                    };
                }

                var map = $.map(emojione.emojioneList, function (_, emoji) {
                    return !options.autocompleteTones ? /_tone[12345]/.test(emoji) ? null : emoji : emoji;
                });
                map.sort();
                editor.textcomplete([
                    {
                        id: css_class,
                        match: /\B(:[\-+\w]*)$/,
                        search: function (term, callback) {
                            callback($.map(map, function (emoji) {
                                return emoji.indexOf(term) === 0 ? emoji : null;
                            }));
                        },
                        template: function (value) {
                            return shortnameTo(value, self.emojiTemplate) + " " + value.replace(/:/g, '');
                        },
                        replace: function (value) {
                            return shortnameTo(value, self.emojiTemplate);
                        },
                        cache: true,
                        index: 1
                    }
                ], textcompleteOptions);

                if (options.textcomplete.placement) {
                    // Enable correct positioning for textcomplete
                    if ($(editor.data('textComplete').option.appendTo).css("position") == "static") {
                        $(editor.data('textComplete').option.appendTo).css("position", "relative");
                    }
                }
            };

            var initAutocomplete = function() {
                if (self.disabled) {
                    var enable = function () {
                        self.off('enabled', enable);
                        autocomplete();
                    };
                    self.on('enabled', enable);
                } else {
                    autocomplete();
                }
            }

            if ($.fn.textcomplete) {
                initAutocomplete();
            }
        }

        if (self.inline) {
            app.addClass(selector('inline', true));
            self.on("@keydown", function(_, e) {
                if (e.which == 13) {
                    e.preventDefault();
                }
            });
        }

        if (/firefox/i.test(navigator.userAgent)) {
            // disabling resize images on Firefox
            document.execCommand("enableObjectResizing", false, false);
        }

        self.isReady = true;
        self.trigger("onLoad", editor);
        self.trigger("ready", editor);
        //}, self.id === 1); // calcElapsedTime()
    };
    var cdn = {
        defaultBase: "https://cdnjs.cloudflare.com/ajax/libs/emojione/",
        defaultBase3: "https://cdn.jsdelivr.net/",
        base: null,
        isLoading: false
    };
    function loadEmojione(options) {
        var emojioneVersion = getEmojioneVersion()
        options = getOptions(options);

        if (!cdn.isLoading) {
            if (!emojione || getSupportMode(detectVersion(emojione)) < 2) {
                cdn.isLoading = true;
                var emojioneJsCdnUrlBase;
                if (getSupportMode(emojioneVersion) > 5) {
                    emojioneJsCdnUrlBase = cdn.defaultBase3 + "npm/emojione@" + emojioneVersion;
                } else if (getSupportMode(emojioneVersion) > 4) {
                    emojioneJsCdnUrlBase = cdn.defaultBase3 + "emojione/" + emojioneVersion;
                } else {
                    emojioneJsCdnUrlBase = cdn.defaultBase + "/" + emojioneVersion;
                }

                $.ajax({
                    url: emojioneJsCdnUrlBase + "/lib/js/emojione.min.js",
                    dataType: "script",
                    cache: true,
                    success: function () {

                        emojione = window.emojione;
                        emojioneVersion = detectVersion(emojione);
                        emojioneSupportMode = getSupportMode(emojioneVersion);
                        var sprite;
                        if (emojioneSupportMode > 4) {
                            cdn.base = cdn.defaultBase3 + "emojione/assets/" + emojioneVersion;
                            sprite = cdn.base + "/sprites/emojione-sprite-" + emojione.emojiSize + ".css";
                        } else {
                            cdn.base = cdn.defaultBase + emojioneVersion + "/assets";
                            sprite = cdn.base + "/sprites/emojione.sprites.css";
                        }
                        if (options.sprite) {
                            if (document.createStyleSheet) {
                                document.createStyleSheet(sprite);
                            } else {
                                $('<link/>', {rel: 'stylesheet', href: sprite}).appendTo('head');
                            }
                        }
                        while (readyCallbacks.length) {
                            readyCallbacks.shift().call();
                        }
                        cdn.isLoading = false;
                    }
                });
            } else {
                emojioneVersion = detectVersion(emojione);
                emojioneSupportMode = getSupportMode(emojioneVersion);
                if (emojioneSupportMode > 4) {
                    cdn.base = cdn.defaultBase3 + "emojione/assets/" + emojioneVersion;
                } else {
                    cdn.base = cdn.defaultBase + emojioneVersion + "/assets";
                }
            }
        }

        emojioneReady(function() {
            var emojiSize = "";
            if (options.useInternalCDN) {
                if (emojioneSupportMode > 4) emojiSize = emojione.emojiSize + "/";

                emojione.imagePathPNG = 'https://s.w.org/images/core/emoji/12.0.0-1/svg/';
                emojione.imagePathSVG = 'https://s.w.org/images/core/emoji/12.0.0-1/svg/';
                emojione.imagePathSVGSprites = cdn.base + "/sprites/emojione.sprites.svg";
                emojione.imageType = options.imageType;
            }
            if (getSupportMode(emojioneVersion) > 4) {
                uniRegexp = emojione.regUnicode;
                emojione.imageType = options.imageType || "png";
            } else {
                uniRegexp = new RegExp("<object[^>]*>.*?<\/object>|<span[^>]*>.*?<\/span>|<(?:object|embed|svg|img|div|span|p|a)[^>]*>|(" + emojione.unicodeRegexp + ")", "gi");
            }
        });
    };
    var EmojioneArea = function(element, options) {
        var self = this;
        loadEmojione(options);
        eventStorage[self.id = ++unique] = {};
        possibleEvents[self.id] = {};
        emojioneReady(function() {
            init(self, element, options);
        });
    };
    function bindEvent(self, event) {
        event = event.replace(/^@/, '');
        var id = self.id;
        if (possibleEvents[id][event]) {
            $.each(possibleEvents[id][event], function(i, ev) {
                // ev[0] = element
                // ev[1] = event
                // ev[2] = target
                $.each($.isArray(ev[0]) ? ev[0] : [ev[0]], function(i, el) {
                    $(el).on(ev[1], function() {
                        var args = slice.call(arguments),
                            target = $.isFunction(ev[2]) ? ev[2].apply(self, [event].concat(args)) : ev[2];
                        if (target) {
                            trigger(self, event, [target].concat(args));
                        }
                    });
                });
            });
            possibleEvents[id][event] = null;
        }
    }

    EmojioneArea.prototype.on = function(events, handler) {
        if (events && $.isFunction(handler)) {
            var self = this;
            $.each(events.toLowerCase().split(' '), function(i, event) {
                bindEvent(self, event);
                (eventStorage[self.id][event] || (eventStorage[self.id][event] = [])).push(handler);
            });
        }
        return this;
    };

    EmojioneArea.prototype.off = function(events, handler) {
        if (events) {
            var id = this.id;
            $.each(events.toLowerCase().replace(/_/g, '.').split(' '), function(i, event) {
                if (eventStorage[id][event] && !/^@/.test(event)) {
                    if (handler) {
                        $.each(eventStorage[id][event], function(j, fn) {
                            if (fn === handler) {
                                eventStorage[id][event].splice(j, 1);
                            }
                        });
                    } else {
                        eventStorage[id][event] = [];
                    }
                }
            });
        }
        return this;
    };

    EmojioneArea.prototype.trigger = function() {
        var args = slice.call(arguments),
            call_args = [this].concat(args.slice(0,1));
        call_args.push(args.slice(1));
        return trigger.apply(this, call_args);
    };

    EmojioneArea.prototype.setFocus = function () {
        var self = this;
        emojioneReady(function () {
            self.editor.focus();
        });
        return self;
    };

    EmojioneArea.prototype.setText = function (str) {
        var self = this;
        emojioneReady(function () {
            self.editor.html(htmlFromText(str, self));
            self.content = self.editor.html();
            trigger(self, 'change', [self.editor]);
            calcButtonPosition.apply(self);
        });
        return self;
    }

    EmojioneArea.prototype.getText = function() {
        return textFromHtml(this.editor.html(), this);
    }

    EmojioneArea.prototype.showPicker = function () {
        var self = this;
        if (self._sh_timer) {
            window.clearTimeout(self._sh_timer);
        }
        self.picker.removeClass("hidden");
        self._sh_timer =  window.setTimeout(function() {
            self.button.addClass("active");
        }, 50);
        trigger(self, "picker.show", [self.picker]);
        return self;
    }

    EmojioneArea.prototype.hidePicker = function () {
        var self = this;
        if (self._sh_timer) {
            window.clearTimeout(self._sh_timer);
        }
        self.button.removeClass("active");
        self._sh_timer =  window.setTimeout(function() {
            self.picker.addClass("hidden");
        }, 500);
        trigger(self, "picker.hide", [self.picker]);
        return self;
    }

    EmojioneArea.prototype.enable = function () {
        var self = this;
        var next = function () {
            self.disabled = false;
            self.editor.prop('contenteditable', true);
            self.button.show();
            var editor = self[(self.standalone) ? "button" : "editor"];
            editor.parent().removeClass('emojionearea-disable');
            trigger(self, 'enabled', [editor]);
        };
        self.isReady ? next() : self.on("ready", next);
        return self;
    }

    EmojioneArea.prototype.disable = function () {
        var self = this;
        self.disabled = true;
        var next = function () {
            self.editor.prop('contenteditable', false);
            self.hidePicker();
            self.button.hide();
            var editor = self[(self.standalone) ? "button" : "editor"];
            editor.parent().addClass('emojionearea-disable');
            trigger(self, 'disabled', [editor]);
        };
        self.isReady ? next() : self.on("ready", next);
        return self;
    }

    $.fn.emojioneArea = function(options) {
        return this.each(function() {
            if (!!this.emojioneArea) return this.emojioneArea;
            $.data(this, 'emojioneArea', this.emojioneArea = new EmojioneArea($(this), options));
            return this.emojioneArea;
        });
    };

    $.fn.emojioneArea.defaults = getDefaultOptions();

    $.fn.emojioneAreaText = function(options) {
        options = getOptions(options);

        var self = this, pseudoSelf = {
            shortnames: (options && typeof options.shortnames !== 'undefined' ? options.shortnames : true),
            emojiTemplate: '<img alt="{alt}" class="emojione' + (options && options.sprite && emojioneSupportMode < 3 ? '-{uni}" src="' + blankImg : 'emoji" src="{img}') + '"/>'
        };

        loadEmojione(options);
        emojioneReady(function() {
            self.each(function() {
                var $this = $(this);
                if (!$this.hasClass('emojionearea-text')) {
                    $this.addClass('emojionearea-text').html(htmlFromText(($this.is('TEXTAREA') || $this.is('INPUT') ? $this.val() : $this.text()), pseudoSelf));
                }
                return $this;
            });
        });

        return this;
    };

}, window ) );
