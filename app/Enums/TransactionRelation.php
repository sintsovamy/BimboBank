<?php

namespace App\Enums;

enum TransactionRelation: string
{
    case SENT = 'sentTransactions';
    case RECEIVED = 'receivedTransactions';
}



