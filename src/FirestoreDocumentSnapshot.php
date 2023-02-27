<?php

namespace Pruvo\LaravelFirestoreConnection;

use Google\Cloud\Core\Timestamp;
use Google\Cloud\Firestore\DocumentReference;
use Google\Cloud\Firestore\DocumentSnapshot;
use Google\Cloud\Firestore\FieldPath;
use Illuminate\Database\Eloquent\Casts\ArrayObject;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class FirestoreDocumentSnapshot extends ArrayObject
{
    /**
     * Database connection name
     * @var string
     */
    protected $connection;

    /**
     * Document path
     * @var string
     */
    protected $path;

    /**
     * Document information, such as create and update timestamps.
     * @var array
     */
    protected $info = [];

    public function setConnection(string $connection)
    {
        $this->connection = $connection;
    }

    public function setPath(string $path)
    {
        $this->path = $path;
    }

    public function setInfo(array $info)
    {
        $this->info = $info;
    }

    public static function fromDocumentSnapshot(string $connection, DocumentSnapshot $documentSnapshot)
    {
        $undoted = [
            "id" => $documentSnapshot->id(),
        ];
        foreach (Arr::dot($documentSnapshot->data()) as $key => $value) {
            if ($value instanceof DocumentReference) {
                $value = $value->path();
            } elseif ($value instanceof Timestamp) {
                $value = Carbon::instance($value->get());
            }
            Arr::set($undoted, $key, $value);
        }
        $arrayObject = new static($undoted);
        $arrayObject->setConnection($connection);
        $arrayObject->setPath($documentSnapshot->reference()->path());
        $arrayObject->setInfo(array_filter([
            'createTime' => ($ct = $documentSnapshot->createTime()) ? Carbon::instance($ct->get()) : null,
            'updateTime' => ($ut = $documentSnapshot->updateTime()) ? Carbon::instance($ut->get()) : null,
            'readTime' => ($rt = $documentSnapshot->readTime()) ? Carbon::instance($rt->get()) : null,
        ]));
        $arrayObject->setFlags(ArrayObject::ARRAY_AS_PROPS);
        return $arrayObject;
    }

    /**
     * Get the reference of the document which created the snapshot.
     *
     * Example:
     * ```
     * $reference = $snapshot->reference();
     * ```
     *
     * @return \Google\Cloud\Firestore\DocumentReference
     */
    public function reference()
    {
        return DB::connection($this->connection)->getClient()->document($this->path);
    }

    /**
     * Get the document name.
     *
     * Names are absolute. The result of this call would be of the form
     * `projects/<project-id>/databases/<database-id>/documents/<relative-path>`.
     *
     * Other methods are available to retrieve different parts of a collection name:
     * * {@see Google\Cloud\Firestore\DocumentSnapshot::id()} Returns the last element.
     * * {@see Google\Cloud\Firestore\DocumentSnapshot::path()} Returns the path, relative to the database.
     *
     * Example:
     * ```
     * $name = $snapshot->name();
     * ```
     *
     * @return string
     */
    public function name()
    {
        return $this->reference()->name();
    }

    /**
     * Get the document path.
     *
     * Paths identify the location of a document, relative to the database name.
     *
     * To retrieve the document ID (the last element of the path), use
     * {@see Google\Cloud\Firestore\DocumentSnapshot::id()}.
     *
     * Example:
     * ```
     * $path = $snapshot->path();
     * ```
     *
     * @return string
     */
    public function path()
    {
        return $this->path;
    }

    /**
     * Get the document identifier (i.e. the last path element).
     *
     * IDs are the path element which identifies a resource. To retrieve the
     * full path to a resource (the resource name)
     *
     * Example:
     * ```
     * $id = $snapshot->id();
     * ```
     *
     * @return string
     */
    public function id()
    {
        return $this->reference()->id();
        $parts = explode('/', trim($this->path, '/'));
        if ($parts[0] === '') {
            return null;
        }

        return end($parts);
    }

    /**
     * Get the Document Update Timestamp.
     *
     * Example:
     * ```
     * $updateTime = $snapshot->updateTime();
     * ```
     *
     * @return \Illuminate\Support\Carbon|null
     */
    public function updateTime()
    {
        return Arr::get($this->info, 'updateTime');
    }

    /**
     * Get the Document Read Timestamp.
     *
     * Example:
     * ```
     * $readTime = $snapshot->readTime();
     * ```
     *
     * @return \Illuminate\Support\Carbon|null
     */
    public function readTime()
    {
        return Arr::get($this->info, 'readTime');
    }

    /**
     * Get the Document Create Timestamp.
     *
     * Example:
     * ```
     * $createTime = $snapshot->createTime();
     * ```
     *
     * @return \Illuminate\Support\Carbon|null
     */
    public function createTime()
    {
        return Arr::get($this->info, 'createTime');
    }

    /**
     * Returns document data as an array, or null if the document does not exist.
     *
     * Example:
     * ```
     * $data = $snapshot->data();
     * ```
     *
     * @return array|null
     */
    public function data()
    {
        return $this->toArray();
    }

    /**
     * Get a subset of the fields.
     *
     * Example:
     * ```
     * $value = $snapshot->only('name', 'email');
     * // or
     * $value = $snapshot->only(['name', 'email']);
     * ```
     *
     * @param array|string $fields The field to retrieve.
     * @return array
     */
    public function only(...$keys)
    {
        return Arr::only($this->data(), is_array($keys[0]) ? $keys[0] : $keys);
    }

    /**
     * Get all fields except for a specified subset.
     *
     * Example:
     * ```
     * $value = $snapshot->except('name', 'email');
     * // or
     * $value = $snapshot->except(['name', 'email']);
     * ```
     *
     * @param array|string $fields The field to exclude.
     * @return array
     */
    public function except(...$keys)
    {
        return Arr::except($this->data(), is_array($keys[0]) ? $keys[0] : $keys);
    }

    /**
     * Returns true if the document exists in the database.
     *
     * Example:
     * ```
     * if ($snapshot->exists()) {
     *     echo "The document exists!";
     * }
     * ```
     *
     * @return bool
     */
    public function exists()
    {
        return true;
    }

    /**
     * Get a field by field path.
     *
     * A field path is a string containing the path to a specific field, at the
     * top level or nested, delimited by `.`. For instance, the value `hello` in
     * the structured field `{ "foo" : { "bar" : "hello" }}` would be accessible
     * using a field path of `foo.bar`.
     *
     * Example:
     * ```
     * $value = $snapshot->get('wallet.cryptoCurrency.bitcoin');
     * ```
     *
     * ```
     * // Field names containing dots or symbols can be targeted using a FieldPath instance:
     * use Google\Cloud\Firestore\FieldPath;
     *
     * $value = $snapshot->get(new FieldPath(['wallet', 'cryptoCurrency', 'my.coin']));
     * ```
     *
     * @param string|\Google\Cloud\Firestore\FieldPath $fieldPath The field path to return.
     * @param mixed $defaultValue The default value to return if the field does not exist.
     * @return mixed
     * @throws \InvalidArgumentException if the field path does not exist.
     */
    public function get($fieldPath, $defaultValue = null)
    {
        if ($fieldPath instanceof FieldPath) {
            $fieldPath = $fieldPath->pathString();
        }

        return Arr::get($this->toArray(), $fieldPath, $defaultValue);
    }

    /**
     * Get a field as DocumentReference by field path.
     * 
     * Alias of get() method. But cast document reference path to DocumentReference instance.
     * 
     * @param mixed $fieldPath 
     * @param mixed $defaultValue 
     * @return \Google\Cloud\Firestore\DocumentReference 
     * @throws InvalidArgumentException 
     */
    public function getAsDocumentReference($fieldPath, $defaultValue = null)
    {
        $value = $this->get($fieldPath, $defaultValue);

        if ($value instanceof DocumentReference) {
            return $value;
        }

        return DB::connection($this->connection)->getClient()->document($value);
    }
}
