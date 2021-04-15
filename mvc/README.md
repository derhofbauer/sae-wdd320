# MVC - Blog System

CMS mit User Login, Image Upload, Kategorien, Kommentare, Sterne-Ratings, Blog Posts, Medien Bibliothek, ...

## Models / Datenbanktabellen

+ User - users
    + id* INT A_I PK
    + username* VARCHAR(255) UK
    + email* VARCHAR(255) UK
    + password* (Hash) VARCHAR(255)
    + is_admin BOOL NULL default:0
    + avatar (file_id) INT NULL FK:files.id
    + crdate* (Creation Date) TIMESTAMP
    + tstamp* (Zeitpunkt des letzten Updates) TIMESTAMP ou_CT
    + deleted_at TIMESTAMP NULL
+ Post (BlogPost) - posts
    + id* INT A_I PK
    + title* VARCHAR(255)
    + slug* VARCHAR(255) UK
    + content TEXT NULL
    + author* (user_id) INT FK:users.id
    + crdate* TIMESTAMP
    + tstamp* (Zeitpunkt des letzten Updates) TIMESTAMP ou_CT
    + deleted_at TIMESTAMP NULL
+ posts_categories_mm
    + id* INT A_I PK
    + post_id* INT FK:posts.id
    + category_id* INT FK:categories.id
+ posts_files_mm
    + id* INT A_I PK
    + post_id* INT FK:posts.id
    + file_id* INT FK:files.id
    + sort INT NULL
+ Category - categories
    + id* INT A_I PK
    + title* VARCHAR(255)
    + slug* VARCHAR(255) UK
    + description TEXT NULL
    + crdate* TIMESTAMP
    + tstamp* (Zeitpunkt des letzten Updates) TIMESTAMP ou_CT
    + deleted_at TIMESTAMP NULL
+ Comment (inkl. Rating) - comments (Es muss entweder content UND/ODER rating geben, beides leer ist nicht erlaubt.)
    + id* INT A_I PK
    + author* (user_id) INT FK:users.id
    + content TEXT NULL
    + post_id* INT FK:posts.id
    + rating INT(5) unsigned NULL
    + parent INT (FK:comments.id)
    + crdate* TIMESTAMP
    + tstamp* (Zeitpunkt des letzten Updates) TIMESTAMP ou_CT
    + deleted_at TIMESTAMP NULL
+ File - files
    + id* INT A_I PK
    + path* TEXT
    + name* TEXT
    + title VARCHAR(255) NULL - Bildname (nicht Dateiname)
    + alttext TEXT NULL
    + caption TEXT NULL - Bildunterschrift
    + is_avatar BOOL NULL default:NULL
    + author* (user_id) INT FK:users.id
    + crdate* TIMESTAMP
    + tstamp* (Zeitpunkt des letzten Updates) TIMESTAMP ou_CT
    + deleted_at TIMESTAMP NULL
+ ...

## Controllers

C Create
R Read/List
U Update
D Delete

+ UserController: CRUD
+ FileController: CRUD
+ PostController: CRUD
+ CategoryController: CRUD
+ CommentController: CR_D

## Core

+ Bootstrap
+ Router
+ Database
+ View
+ Config
+ Session
+ Validator
+ AbstractModel
+ AbstractUser
+ AbstractFile
+ SoftDelete Trait
+ ...

## Views / Seiten

+ Post Liste (Startseite)
+ Post Detail (Single Post)
+ Category Liste
  + Category Post Liste
+ Login
+ Sign-Up
+ Admin: Startseite
+ Admin: Category Liste
+ Admin: Category Edit
+ Admin: Category Create
+ Admin: User Liste
+ Admin: User Edit
+ Admin: User Create
+ Admin: Post Liste
+ Admin: Post Edit
+ Admin: Post Create
+ Admin: Files Liste (Medienbibliothek)
+ Admin: Files Edit
+ Admin: Files Upload
+ Admin: Comments Liste

