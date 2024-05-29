<?php

namespace App\Observers;

use App\Models\Document;
class DocumentObserver
{
    /**
     * Handle the document "created" event.
     *
     * @param  \App\Models\Document  $document
     * @return void
     */
    public function created(Document $document)
    {
        saveLogs('Document Uploaded '.$document->document_name, $document);
    }

    /**
     * Handle the document "updated" event.
     *
     * @param  \App\Models\Document  $document
     * @return void
     */
    public function updated(Document $document)
    {
        saveLogs('Document updated '.$document->document_name, $document);
    }

    /**
     * Handle the document "deleted" event.
     *
     * @param  \App\Models\Document  $document
     * @return void
     */
    public function deleted(Document $document)
    {
        saveLogs('Document softdeleted '.$document->document_name, $document);
    }

    /**
     * Handle the document "restored" event.
     *
     * @param  \App\Models\Document  $document
     * @return void
     */
    public function restored(Document $document)
    {
        saveLogs('Document restored '.$document->document_name, $document);
    }

    /**
     * Handle the document "force deleted" event.
     *
     * @param  \App\Models\Document  $document
     * @return void
     */
    public function forceDeleted(Document $document)
    {
        saveLogs('Document force deleted '.$document->document_name, $document);
    }

}
