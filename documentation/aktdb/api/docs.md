---
title: Aktivws API
brand: aktivws
version: 0.1.0
---

# Aktivws API

### All API calls start with

<pre class="base">
http://dds.dk/aktivws_proxy
</pre>

### Path

For this documentation, we will assume every request begins with the above path.

### Authentication

API calls with the label: 'protected' needs authentication.

Authentication is done in the file 'cookieauth.php', and is implemented by checking on the cookie named 'roles' in the request. Based on the user's roles, the user's permissions are evaluated, and only if the user has permission to perform a certain task he/she will get a response with content. Since cookies are easy to manipulate for a client, all requests to webservices are done through Drupal, and only requests from Drupal's IP are permitted. This means that manipulating cookies on the client side makes no difference, as all roles for a given user are decided by Drupal.

### Format

All calls are returned in **JSON**.

### Status Codes

- **200** Successful GET POST and PUT.
- **403** Permission denied.
- **417** Unsuccesful POST.
          The API recieves unexpected data/parameters in conjunction with the "contract".

# Activity

## GET /activities

Returns all active activities. Ordering is per default by title.

### Filters:
* q (query) - A substring to search for. Search is performed in title and body.
* age - The span of ages that the activity is targeting.
* type - The type of activity.
* duration - The duration of the activity.
* orderby - What to order activity list by. Options:
  * title - Alphabetical order.
  * popularity - The weight of the activity. High weight equals low popularity.
  * date - Chronological order.
* limit - Used for pagination. Ex.: 0-20.
* author - DDS user id.


## GET /activities/:id

Get one activity by activity id.

#### Example request

    /activities/15

#### Example response

    {
      id: "15",
      title: "Varedeklarationsbilkort",
      body: "En sjov måde at gøre spejderne bevidst om hvad der er i forskellige varer.",
      updated: "1354677186",
      created: "1354201544",
      dds_uid: "744",
      weight: "105",
      instructions: "<p>Ud af karton laver man en masse kort i samme størrelse.</p><p>Spejderne klipper varedeklarationer ud af forskellige varer og klistrer bagpå kortene.</p><p>Når alle spejderne har lavet sine kort, skal der spilles:</p><p>&nbsp;</p><p>Det gælder om at få alle de andres kort.</p><p>&nbsp;</p><p>Man lægger sine kort i en bunke, med varedeklerationen nedad.</p><p>&nbsp;</p><p>Den, hvis tur det er, siger en egenskab, f.eks. &ldquo;mindst fedt&rdquo; eller &ldquo;flest proteiner&rdquo;</p><p>&nbsp;</p><p>Den, der har eks. den laveste værdi af fedt eller højeste værdi af proteiner, vinder</p><p>&nbsp;</p><p>Vinderen får alle de andres kort, og skal lægge dem nederst i sin bunke.</p><p>&nbsp;</p><p>Herefter er det vinderens tur</p>",
      materials: "<p>Varedeklarationer fra forskellige varer &ndash; spejderne kan evt. få til opgave at medbringe dem hjemmefra.<br />Karton til kort<br />Limstifter<br />Sakse</p>",
      active: "1",
      image_1: {
      id: "209",
      src: "50bebbc20a95e.jpg"
      },
      age: {
      3: {
      id: "3",
      title: "Mini"
      },
      4: {
      id: "4",
      title: "Junior"
      },
      5: {
      id: "5",
      title: "Trop"
      }
      },
      type: {
      5: {
      id: "5",
      title: "Sundhed"
      },
      10: {
      id: "10",
      title: "Leg"
      }
      },
      duration: {
      3: {
      id: "3",
      title: "30-60 minutter",
      minutes: "60"
      }
      },
      image_2: [ ],
      image_3: [ ],
      image_4: [ ],
      image_5: [ ],
      image_6: [ ],
      question_1: "Hvad har spejderne fået ud af aktiviteten?",
      question_2: "Hvordan var din rolle som leder i aktiviteten?",
      question_3: "",
      question_4: "Hvad har du lært af denne aktivitet?",
      question_5: "Hvad har de andre gjort for at det blev en god aktivitet for dig?",
      question_6: "",
      youtube: [ ]
    }


<a id="post_activity"></a>
## POST /activity/:id (protected)

Creates an activity.

#### Example request

    {
      "id": "",
      "dds_uid": "1337",
      "title": "title",
      "body": "body",
      "age": {
        "1": "1",
        "2": "2",
      },
      "type": {
        "1": "1",
        "2": "2",
      },
      "duration": "4",
      "instructions": "<p>instructions<\/p>",
      "materials": "<p>materials<\/p>",
      "image_1": "5158a3a93e5a6.jpg",
      "image_2": "5158a3a945ad5.jpg",
      "image_3": 0,
      "image_4": 0,
      "image_5": 0,
      "image_6": 0,
      "question_1": "Hvis du skulle lave aktiviteten igen, er der så noget du ville gøre anderledes?",
      "question_2": "",
      "question_3": "",
      "question_4": "Hvad har du lært af denne aktivitet?",
      "question_5": "",
      "question_6": "",
      "youtube": "http:\/\/www.youtube.com\/watch?v=9bZkp7q19f0",
      "img_urls": ["aktivdb.fe.dev.cd.adapt.dk\/sites\/default\/files\/aktivws\/5158a3a93e5a6.jpg", "aktivdb.fe.dev.cd.adapt.dk\/sites\/default\/files\/aktivws\/5158a3a945ad5.jpg"]
    }

## PUT /activity/:id (protected)

Updates an activity.

#### Example request
    
See [POST /activity/:id](#post_activity)

## DELETE /activity/:id (protected)

Deletes an activity.

#### Example request

/activity/248

# Activity type

## GET /activity_types

Lists all possible activity_types.

#### Example response

    [
      {
      id: "1",
      title: "Pioner og tovværk"
      },
      {
      id: "2",
      title: "Orientering"
      },
      {
      id: "3",
      title: "Klar dig selv"
      },
      {
      id: "4",
      title: "Samarbejdsøvelser"
      },
      {
      id: "5",
      title: "Sundhed"
      },
      {
      id: "6",
      title: "Kommunikation"
      },
      {
      id: "7",
      title: "Vandaktiviteter"
      },
      {
      id: "8",
      title: "Natur og friluftsliv"
      },
      {
      id: "9",
      title: "Madlavning"
      },
      {
      id: "10",
      title: "Leg"
      },
      {
      id: "11",
      title: "Drama og Musik"
      },
      {
      id: "12",
      title: "Klima og Miljø"
      },
      {
      id: "13",
      title: "Kultur og samfund"
      },
      {
      id: "14",
      title: "Kreativitet og håndelag"
      },
      {
      id: "15",
      title: "Evaluering og refleksion"
      }
    ]

## GET /activity_types/:id

Exposes one activity type.

#### Example request

    activity_types/5

#### Example response

    {
      id: "5",
      title: "Sundhed"
    }

## POST /activity_type (protected)

Saves new activity type.

#### Example request

    {
      title: "Sundhed"
    }

## PUT /activity_type/:id (protected)

Updates an activity type.

#### Example request

    {
      "id": "5",
      "title": "Madlavning"
    }

## DELETE /activity_type/:id (protected)

Deletes an activity type.

# Age

## GET /ages

Lists all possible ages.

#### Example response

    [
      {
      id: "1",
      title: "Mikrobe"
      },
      {
      id: "2",
      title: "Mikro"
      },
      {
      id: "3",
      title: "Mini"
      },
      {
      id: "4",
      title: "Junior"
      },
      {
      id: "5",
      title: "Trop"
      },
      {
      id: "6",
      title: "Senior"
      },
      {
      id: "7",
      title: "Leder"
      }
    ]

## GET /ages/:id

Exposes one age.

#### Example request

    ages/7

#### Example response

    {
      id: "7",
      title: "Leder"
    }

## POST /age (protected)

Saves new age.

#### Example request

    {
      title: "Junior"
    }

## PUT /age/:id (protected)

Updates an age.

#### Example request

    {
      "id": "7",
      "title": "Superleder"
    }

## DELETE /age/:id (protected)

Deletes an age.

# Content types

## GET /content_types

Lists all API endpoints.

#### Example response

    [
      "activity",
      "statistics",
      "favorite",
      "activity_type",
      "age",
      "duration_interval",
      "image",
      "video",
      "lquestion_template",
      "squestion_template",
      "meeting_item_element",
      "meeting"
    ]

## GET /content_types/:type

Shows available fields of a content type.

#### Example request

    /content_types/activity

#### Example response

    {
      id: {
        type: "hidden",
        form_element: true
      },
      dds_uid: {
        type: "hidden",
        form_element: true,
        required: true
      },
      title: {
        type: "textfield",
        title: "Title",
        description: "The title of the activity.",
        form_element: true,
        required: true
      },
      body: {
        type: "textarea",
        maxlength: 160,
        title: "Summary",
        description: "A description of the activity.",
        form_element: true,
        required: true
      },
      image_1: {
        type: "image_upload",
        title: "Image 1",
        description: "The primary image for the activity",
        form_element: true,
        required: false,
        relation: {
          type: "many2many",
          target: "image",
          value_field: "src"
        }
      },
      age: {
        type: "integer",
        title: "Age",
        description: "The age that the activity targets.",
        form_element: true,
        options: {
          choice: "multiple",
          location: "/ages"
        },
        relation: {
          type: "many2many",
          target: "age"
        },
        required: true
      },
      type: {
        type: "integer",
        title: "Activity type",
        description: "The type of activity.",
        form_element: true,
        options: {
          choice: "multiple",
          location: "/activity_types"
        },
        relation: {
          type: "many2many",
          target: "activity_type"
        },
        required: true
      },
      duration: {
        type: "integer",
        title: "Duration",
        description: "The duration of the activity.",
        form_element: true,
        options: {
          choice: "single",
          location: "/duration_intervals"
        },
        relation: {
          type: "one2many",
          target: "duration_interval"
        },
        required: true
      },
      instructions: {
        type: "text_format",
        title: "Instructions",
        description: "Instructions on how to carry out the activity",
        form_element: true,
        required: true
      },
      materials: {
        type: "text_format",
        title: "Materials",
        description: "Relevant materials needed for the activity",
        form_element: true
      },
      image_2: {
        type: "image_upload",
        title: "Image 2",
        description: "An activity image",
        form_element: true,
        relation: {
          type: "many2many",
          target: "image",
          value_field: "src"
        }
      },
      image_3: {
        type: "image_upload",
        title: "Image 3",
        description: "An activity image",
        form_element: true,
        relation: {
          type: "many2many",
          target: "image",
          value_field: "src"
        }
      },
      image_4: {
        type: "image_upload",
        title: "Image 4",
        description: "An activity image",
        form_element: true,
        relation: {
          type: "many2many",
          target: "image",
          value_field: "src"
        }
      },
      image_5: {
        type: "image_upload",
        title: "Image 5",
        description: "An activity image",
        form_element: true,
        relation: {
          type: "many2many",
          target: "image",
          value_field: "src"
        }
      },
      image_6: {
        type: "image_upload",
        title: "Image 6",
        description: "An activity image",
        form_element: true,
        relation: {
          type: "many2many",
          target: "image",
          value_field: "src"
        }
      },
      question_1: {
        type: "textfield",
        maxlength: 160,
        title: "Question (for leader) 1",
        description: "Question for leader",
        form_element: true,
        required: true,
        relation: {
          type: "many2many",
          target: "question",
          value_field: "text"
        }
      },
      question_2: {
        type: "textfield",
        maxlength: 160,
        title: "Question (for leader) 2",
        description: "Question for leader",
        form_element: true,
        relation: {
          type: "many2many",
          target: "question",
          value_field: "text"
        }
      },
      question_3: {
        type: "textfield",
        maxlength: 160,
        title: "Question (for leader) 3",
        description: "Question for leader",
        form_element: true,
        relation: {
          type: "many2many",
          target: "question",
          value_field: "text"
        }
      },
      question_4: {
        type: "textfield",
        maxlength: 160,
        title: "Question (for scout) 1",
        description: "Question for scout",
        form_element: true,
        required: true,
        relation: {
          type: "many2many",
          target: "question",
          value_field: "text"
        }
      },
      question_5: {
        type: "textfield",
        maxlength: 160,
        title: "Question (for scout) 2",
        description: "Question for scout",
        form_element: true,
        relation: {
          type: "many2many",
          target: "question",
          value_field: "text"
        }
      },
      question_6: {
        type: "textfield",
        maxlength: 160,
        title: "Question (for scout) 3",
        description: "Question for scout",
        form_element: true,
        relation: {
          type: "many2many",
          target: "question",
          value_field: "text"
        }
      },
      youtube: {
        type: "textfield",
        maxlength: 255,
        title: "Youtube id",
        description: "Id på youtube film",
        form_element: true,
        relation: {
          type: "many2many",
          target: "video",
          value_field: "src"
        }
      }
    }

## GET /content_types/:type/locations

Get the API uris of a content type.

#### Example request

    /content_types/activity/locations

#### Example response
    {
      GET: "activities",
      POST: "activity",
      PUT: "activity",
      DELETE: "activity"
    }

# Duration interval

## GET /duration_intervals

Lists all possible duration intervals.

#### Example response

    [
      {
      id: "1",
      title: "<15 minutter",
      minutes: "15"
      },
      {
      id: "2",
      title: "15-30 minutter",
      minutes: "30"
      },
      {
      id: "3",
      title: "30-60 minutter",
      minutes: "60"
      },
      {
      id: "4",
      title: "60-90 minutter",
      minutes: "90"
      },
      {
      id: "5",
      title: "90-120 minutter",
      minutes: "120"
      },
      {
      id: "6",
      title: "120+ minutter",
      minutes: "150"
      }
    ]

## GET /duration_intervals/:id

Exposes one age.

#### Example request

    /duration_intervals/4

#### Example response

    {
      id: "4",
      title: "60-90 minutter",
      minutes: "90"
    }

## POST /duration_interval (protected)

Saves new duration interval.

#### Example request

    {
      title: "90-120 minutter",
      minutes: 120
    }

## PUT /duration_interval/:id (protected)

Updates a duration interval.

#### Example request

    {
      "id": "5",
      title: "90-130 minutter",
      minutes: 130
    }

## DELETE /duration_interval/:id (protected)

Deletes a duration interval.

# Favorite

## GET /favorites/:dds_uid

Get all favorites from a user.

#### Example request

    {
      dds_uid: 1337,
    }

#### Example response

    {
      data: [
        {
          id: "245",
          created: "1359555243",
          title: "Woop App: Tag et spil Bombe!",
          body: "Med spejdernes Woop App kan du tage et spil Bombe, hvor man leger fangeleg med brugernes smartphones. Efter et uvist antal minutter springer bomben!",
          type: "Kommunikation, Leg",
          duration: "<15 minutter",
          age: "Junior, Trop, Senior",
          img: "51092aa99a013.png,51092aa9b627f.jpeg,51092aa9cc742.jpeg,51092aa9e45a6.jpeg"
        },
        {
          id: "243",
          created: "1355680674",
          title: "Rytmeleg i rundkreds",
          body: "Hyg jer med en rytmeleg, slut evt. mødet af på denne måde - med grin, musik og latter",
          type: "Drama og Musik",
          duration: "<15 minutter",
          age: "Mini, Junior, Trop",
          img: "50ce0bc905761.jpg"
        },
        {
          id: "234",
          created: "1355667384",
          title: "Dværgtræer",
          body: "Gå ud i skoven og find nogle små træer, som I kan tage med tilbage til spejderhytten, hvor I skal lave dværgtræer",
          type: "Natur og friluftsliv",
          duration: "120+ minutter",
          age: "Mikrobe, Mikro, Mini, Junior",
          img: "50cdd87f81721.JPG"
        },
        {
          id: "41",
          created: "1354280726",
          title: "Smag på angola",
          body: "Formålet med at lave angolansk mad, er at spejderne prøver at leve sig ind i hvad og hvordan angolanske børn spiser.",
          type: "Madlavning",
          duration: "90-120 minutter",
          age: "Mini, Junior, Senior, Trop, Leder",
          img: "50beb3e22d914.jpg"
        }
      ]
    }

## GET /favorite/:dds\_uid/:activity\_id (protected)

Find out if a user has a certain activity as a favorite.
Returns activity id if positive or else http code 417.

#### Example request

    {
      dds_uid: 1224,
      activity_id: 12
    }

#### Example response

    {
      id: 12
    }

## POST /favorite (protected)

#### Example request

    {
      dds_uid: 1337,
      activity_id: 234
    }

#### Example response

    {
      "content": {
        "id": 47,
        "dds_uid": "1337",
        "activity_id": "234"
      },
      "message": "favorite was created"
    }

## DELETE /favorite/:id (protected)

Delete a favorite

#### Example request

    /favorite/46

#### Example response

    {
      "content": {
        "id": "46",
        "dds_uid": "1337",
        "activity_id": "234"
      },
      "message": "favorite was deleted"
    }

# Image

## GET /images/:id

Get filename for a particular image id.

#### Example request

    /images/286

#### Example response
    
    {
      id: 286,
      src: "50c1048f30263.jpg"
    }

# Meeting

## GET /meetings/:id

Get a particular meeting by id.

#### Example request

    /meetings/60

#### Example response
    
    {
      id: "60",
      updated: "1360789560",
      created: "1360789560",
      title: "nyt møde 0930",
      description: "sdfsdsdffsd",
      start: "17:25",
      duration: "0",
      dds_uid: "1337",
      ownMeeting_items: {
        460: {
          id: "460",
          weight: "0",
          meeting_id: "60"
        },
        461: {
          id: "461",
          weight: "1",
          meeting_id: "60"
        },
        462: {
          id: "462",
          weight: "2",
          meeting_id: "60"
        }
      },
      items: [{
          id: "98",
          comment: null,
          duration: null,
          activity_id: null,
          meeting_items_id: "460",
          title: null,
          type: "activity"
        }, {
          id: "120",
          description: "",
          duration: "0",
          meeting_items_id: "461",
          title: "",
          type: "song"
        }, {
          id: "107",
          description: "",
          duration: "0",
          meeting_items_id: "462",
          title: "",
          type: "play"
        }
      ]
    }

## POST /meeting (protected)

Save a meeting.

#### Example request

    {
      "dds_uid": "1337",
      "title": "miktest1013",
      "description": "zxczxczxczxcv",
      "start": "19:00",
      "duration": "50",
      "items": {
        "1364976835": {
          "description": "Så starter mødet",
          "duration": "20",
          "type": "meeting_item_start"
        },
        "1364976849": {
          "title": "Så er der en sang",
          "description": "adashdashdkashdkasdh",
          "duration": "20",
          "type": "meeting_item_song"
        },
        "1364976867": {
          "description": "Så slutter mødet",
          "duration": "10",
          "type": "meeting_item_end"
        }
      }
    }

#### Example response

    {
      "id": "",
      "dds_uid": "1337",
      "title": "miktest1013",
      "description": "zxczxczxczxcv",
      "start": "19:00",
      "duration": "50",
      "items": {
        "1364976835": {
          "description": "Så starter mødet",
          "duration": "20",
          "type": "meeting_item_start"
        },
        "1364976849": {
          "title": "Så er der en sang",
          "description": "adashdashdkashdkasdh",
          "duration": "20",
          "type": "meeting_item_song"
        },
        "1364976867": {
          "description": "Så slutter mødet",
          "duration": "10",
          "type": "meeting_item_end"
        }
      }
    }


## PUT /meeting/:id (protected)

Update a meeting.

#### Example request

    {
      "id": "70",
      "dds_uid": "1337",
      "title": "miktest1020",
      "description": "zxczxczxczxcv",
      "start": "19:00",
      "duration": "50",
      "items": {
        "506": {
          "description": "Så starter mødet",
          "duration": "20",
          "type": "meeting_item_start"
        },
        "507": {
          "title": "Så er der en sang",
          "description": "adashdashdkashdkasdh",
          "duration": "20",
          "type": "meeting_item_song"
        },
        "508": {
          "description": "Så slutter mødet",
          "duration": "10",
          "type": "meeting_item_end"
        }
      }
    }

#### Example response

    {
      "content": {
        "id": "70",
        "updated": 1364977219,
        "created": "1364976898",
        "title": "miktest1020",
        "description": "zxczxczxczxcv",
        "start": "19:00",
        "duration": "50",
        "dds_uid": "1337"
      },
      "message": "meeting was updated"
    }

## DELETE /meeting/:id (protected)

Delete a particular meeting by id.

#### Example request

    DELETE /meeting/70

#### Example response

    {
      "content": {
        "id": "70",
        "updated": "1364977219",
        "created": "1364976898",
        "title": "miktest1020",
        "description": "zxczxczxczxcv",
        "start": "19:00",
        "duration": "50",
        "dds_uid": "1337"
      },
      "message": "meeting was deleted"
    }

# Meeting item elements

## GET /meeting\_item\_elements

#### Example request
    
    /meeting_item_elements

#### Example response

    {
      start: "Meeting start",
      activity: "Activity",
      song: "Song",
      play: "Play",
      break: "Break",
      other: "Other",
      end: "End of meeting"
    }

## GET /meeting\_item\_elements/:id

#### Example request

    /meeting_item_elements/1

#### Example response

    {
      id: "1",
      machine_name: "start",
      title: "Meeting start"
    }

# Question templates

## GET /lquestion_templates

Get all leader question templates.

#### Example request

  /lquestion_templates

#### Example response

    [
      {
      id: "1",
      caption: "Hvad har spejderne fået ud af aktiviteten?"
      },
      {
      id: "2",
      caption: "Hvis du skulle lave aktiviteten igen, er der så noget du ville gøre anderledes?"
      },
      {
      id: "3",
      caption: "Hvad gjorde at det var en god aktivitet?"
      },
      {
      id: "4",
      caption: "Hvordan har aktiviteten udfordret spejderne?"
      },
      {
      id: "5",
      caption: "Hvordan var din rolle som leder i aktiviteten?"
      },
      {
      id: "6",
      caption: "Hvad fungerede godt?"
      },
      {
      id: "7",
      caption: "Hvad har du lært om spejderne?"
      },
      {
      id: "8",
      caption: "Hvilke af følgende udviklingsområder har aktiviteten ramt: Socialt, fysisk, intellektuelt, følelsesmæssigt, kreativt, spirituelt"
      },
      {
      id: "9",
      caption: "I hvor høj grad opfyldte aktiviteten det formål du havde med den?"
      },
      {
      id: "10",
      caption: "Hvilken af spejdermetoderne er mest i spil i denne aktivitet?"
      },
      {
      id: "11",
      caption: "Hvilken rolle havde patruljens PL og PA under aktiviteten? "
      },
      {
      id: "17",
      caption: "Hvilke punkter i spejderloven passer til aktiviteten?"
      },
      {
      id: "18",
      caption: "Hvis du var på samme alder som spejderne, hvad ville du så have fået ud af aktiviteten?"
      }
    ]

# Question templates

## GET /squestion_templates

Get all scout question templates.

#### Example request

  /squestion_templates

#### Example response

    [
      {
      id: "1",
      caption: "Hvad har du lært af denne aktivitet?"
      },
      {
      id: "2",
      caption: "Hvis du skulle lave aktiviteten igen, er der så noget du ville gøre anderledes?"
      },
      {
      id: "3",
      caption: "Hvad gjorde at det var en god aktivitet?"
      },
      {
      id: "4",
      caption: "Hvordan har aktiviteten udfordret dig?"
      },
      {
      id: "5",
      caption: "Hvilket af følgende ord synes du passer bedst til aktiviteten? Sjov, kammeratskab, udfordring, samarbejde, oplevelse, friluftsliv"
      },
      {
      id: "6",
      caption: "Hvad har de andre gjort for at det blev en god aktivitet for dig?"
      },
      {
      id: "7",
      caption: "Hvad fungerede godt?"
      },
      {
      id: "8",
      caption: "Hvad kunne man gøre for at det blev en endnu bedre aktivitet?"
      },
      {
      id: "10",
      caption: "Hvad gjorde du for at hjælpe patruljen godt igennem aktiviteten?"
      },
      {
      id: "11",
      caption: "Hvad lærte du af denne aktivitet om dit samarbejde med andre?"
      },
      {
      id: "12",
      caption: "Hvad tror I var jeres leders formål med denne aktivitet?"
      },
      {
      id: "13",
      caption: "Var hele patruljen aktiv under aktiviteten? Hvis ikke: Hvad var så årsagen og hvordan kunne det gøres endnu bedre? "
      },
      {
      id: "14",
      caption: "Tog patruljen ansvar for aktiviteten? Både før (forberedelse), under (afvikling) og efter (oprydning)"
      },
      {
      id: "15",
      caption: "Hvordan samarbejdede I under aktiviteten? Er I tilfredse med det, eller kunne noget være gjort anderledes?"
      },
      {
      id: "16",
      caption: "Alle spejdere er forskellige. Tal om hvad I hver især er gode til og tænk over om I udnyttede denne viden under aktiviteten"
      }
    ]

# Statistics

Statistics handles user interactions with activities. This means that every time a user requests a certain activity, this request is stored with the user's 'dds\_uid', the activity id and a timestamp. This makes it possible to list a user's most recently visited activities, and is also used to calculate the popularity of each activity.

When an activity is deleted, all registered statistics on that specific activity is deleted as well to avoid inconsistent data.

## GET /stats

Get the registered 'statistics' (activity ids and timestamps) for a user, where the dds user is indicated by the 'dds\_uid' cookie. 

### Filters:

* The 'latest' parameter is no longer used.
* The 'title' parameter indicates whether the title and image of each activity should be returned along with the activity ids and timestamps.
* The 'limit' parameter indicates if the number of returned activities should be limited to a certain number.

### Example request

    /stats?limit=0-5&latest=true&title=true

### Example response

    [
      {
        id: 234,
        title: "Dv\u00e6rgtr\u00e6er",
        img: "50cdd87f81721.JPG"
      },
      {
        id:26,
        title: "Aktiv transport",
        img: "50be656d7e4fd.png"
      },
      {
        id: 138,
        title: "Affaldslinjen",
        img: "50cdde462343a.JPG,50cdde464ab60.JPG"
      },
      {
        id: 85,
        title:"\u00c5nden i flasken - og ballonen",
        img:"50bf414bbd73a.jpg"
      },
      {
        id: 238,
        title: "Ja\/nej stafet",
        img: "50ce551323d31.jpg"
      }
    ]

# Video

## GET /videos

Get all available videos.

#### Example request

    /videos

#### Example response
    
    [
      {
        id: "1",
        src: "http://www.youtube.com/watch?v=9bZkp7q19f0"
      },
      {
        id: "2",
        src: "http://youtu.be/9_LLj4_3ZRA"
      }
    ]

## GET /videos/:id

Get a particular video by id.

#### Example request

    /videos/2

#### Example response
    
    {
      id: "2",
      src: "http://youtu.be/9_LLj4_3ZRA"
    }
