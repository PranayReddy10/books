<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Proof that a reader has already earned their uploader coins for a book.
 * The (book_id, reader_id) unique index is the real guard — inserting is how we
 * claim the credit, so two simultaneous reads cannot both pay out.
 */
class BookReadCredit extends Model
{
    protected $table = 'book_read_credits';

    protected $fillable = ['book_id', 'reader_id', 'uploader_id', 'coins'];
}
