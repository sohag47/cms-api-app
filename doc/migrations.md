# Learning Laravel Migrations

Create Migration Command
```bash
php artisan make:migration create_users_table
```

Running Migrations
```bash
php artisan migrate
```

Check which migrations have run
```bash
php artisan migrate:status
```

To roll back the last migration batch
```bash
php artisan migrate:rollback
```

To roll back all migrations:
```bash
php artisan migrate:reset
```

To refresh migrations (rollback and re-run)
```bash
php artisan migrate:refresh
```

Schema Dumping (Recommended for Mature Projects)
```bash
php artisan schema:dump
```

Column Types
```bash
$table->bigIncrements('id');             // Auto-incrementing BIGINT (primary key)
$table->bigInteger('votes');              // BIGINT
$table->binary('data');                   // BLOB
$table->boolean('confirmed');             // BOOLEAN
$table->char('name', 100);                // CHAR with length
$table->date('created_at');               // DATE
$table->dateTime('created_at');           // DATETIME
$table->decimal('amount', 8, 2);          // DECIMAL
$table->double('value', 8, 2);            // DOUBLE
$table->enum('role', ['admin', 'user']);  // ENUM
$table->float('cost', 8, 2);              // FLOAT
$table->geometry('position');             // GEOMETRY
$table->geometryCollection('positions');  // GEOMETRYCOLLECTION
$table->increments('id');                 // Auto-incrementing INT (primary key)
$table->integer('age');                   // INT
$table->ipAddress('visitor');             // IP address
$table->json('options');                  // JSON
$table->jsonb('options');                 // JSONB (Postgres)
$table->lineString('line');               // LINESTRING
$table->longText('description');          // LONGTEXT
$table->macAddress('device');             // MAC address
$table->mediumIncrements('id');           // Auto-incrementing MEDIUMINT
$table->mediumInteger('number');          // MEDIUMINT
$table->mediumText('bio');                // MEDIUMTEXT
$table->morphs('taggable');               // taggable_id, taggable_type (for polymorphic relations)
$table->multiLineString('lines');         // MULTILINESTRING
$table->multiPoint('points');             // MULTIPOINT
$table->multiPolygon('polygons');         // MULTIPOLYGON
$table->nullableMorphs('taggable');       // Same as morphs, but nullable
$table->nullableTimestamps();             // created_at, updated_at nullable
$table->point('position');                // POINT
$table->polygon('area');                  // POLYGON
$table->rememberToken();                  // remember_token VARCHAR(100) NULL
$table->set('flavors', ['vanilla', 'chocolate']); // SET (MySQL)
$table->smallIncrements('id');            // Auto-incrementing SMALLINT
$table->smallInteger('votes');            // SMALLINT
$table->softDeletes();                    // deleted_at TIMESTAMP NULL
$table->softDeletesTz();                  // deleted_at TIMESTAMP(NULL) with timezone
$table->string('name', 255);              // VARCHAR
$table->text('description');              // TEXT
$table->time('sunrise');                  // TIME
$table->timestamp('added_on');            // TIMESTAMP
$table->timestampTz('added_on');          // TIMESTAMP with timezone
$table->timestamps();                     // created_at, updated_at TIMESTAMP
$table->timestampsTz();                   // created_at, updated_at TIMESTAMP(TZ)
$table->tinyIncrements('id');             // Auto-incrementing TINYINT
$table->tinyInteger('votes');             // TINYINT
$table->unsignedBigInteger('votes');      // UNSIGNED BIGINT
$table->unsignedDecimal('amount', 8, 2);  // UNSIGNED DECIMAL
$table->unsignedInteger('votes');         // UNSIGNED INT
$table->unsignedMediumInteger('votes');   // UNSIGNED MEDIUMINT
$table->unsignedSmallInteger('votes');    // UNSIGNED SMALLINT
$table->unsignedTinyInteger('votes');     // UNSIGNED TINYINT
$table->uuid('id');                       // UUID
```

Modifiers (Chainable Methods)
```bash
$table->nullable();           // Allows NULL values
$table->default($value);      // Sets a default value
$table->unique();             // Unique constraint
$table->index();              // Adds an index
$table->primary();            // Sets as primary key
$table->unsigned();           // Makes integer unsigned
$table->comment('text');      // Adds comment to column
$table->after('column');      // Place after another column
$table->change();             // Used when modifying column
```

Foreign Key Constraints
```bash
$table->foreign('user_id')->references('id')->on('users');
$table->foreignId('user_id')->constrained();                   // shorthand for foreign key
$table->foreignId('user_id')->nullable()->constrained();
$table->foreignIdFor(User::class);                             // uses model for foreign key
$table->dropForeign(['user_id']);                              // drops foreign key constraint// Used when modifying column
```

Index Methods
```bash
$table->primary('id');
$table->unique('email');
$table->index('status');
$table->dropPrimary('users_id_primary');
$table->dropUnique('users_email_unique');
$table->dropIndex('users_status_index');
```

Special Table Methods
```php
Schema::create('table', function (Blueprint $table) { ... });
Schema::drop('table');
Schema::dropIfExists('table');
Schema::rename('from', 'to');
Schema::table('table', function (Blueprint $table) { ... });
```

Dropping Columns/Indexes
```php
$table->dropColumn('column');
$table->dropUnique(['column']);
$table->dropIndex(['column']);
$table->dropPrimary(['column']);
```

Other Useful Methods
```php
$table->rememberToken();       // For authentication
$table->softDeletes();         // For soft delete pattern
$table->timestamps();          // Adds created_at and updated_at
$table->nullableTimestamps();  // Timestamps but nullable
$table->morphs('imageable');   // For polymorphic relationships
```