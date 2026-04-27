<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Stored path in storage/app/public/po-documents/
            $table->string('po_document_path')->nullable()->after('approved_at');

            // Track mail delivery status per recipient type
            $table->timestamp('school_mail_sent_at')->nullable()->after('po_document_path');
            $table->timestamp('sp_mail_sent_at')->nullable()->after('school_mail_sent_at');
            $table->timestamp('accounts_mail_sent_at')->nullable()->after('sp_mail_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'po_document_path',
                'school_mail_sent_at',
                'sp_mail_sent_at',
                'accounts_mail_sent_at',
            ]);
        });
    }
};
