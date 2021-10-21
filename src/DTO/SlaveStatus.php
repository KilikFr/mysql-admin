<?php

namespace App\DTO;

use Gedmo\Timestampable\Traits\Timestampable;

class SlaveStatus
{
    use Timestampable;

    public const STATUS_UNKNOWN = null;
    public const STATUS_ERROR = 0;
    public const STATUS_OK = 1;

    public const STATUSES = [
        self::STATUS_UNKNOWN,
        self::STATUS_ERROR,
        self::STATUS_OK,
    ];

    private ?int $status = self::STATUS_UNKNOWN;

    private ?string $slaveIoState;
    private ?string $masterHost;
    private ?string $masterUser;
    private ?int $masterPort;
    private ?int $connectRetry;
    private ?string $masterLogFile;
    private ?int $readMasterLogPos;
    private ?string $relayLogFile;
    private ?int $relayLogPos;
    private ?string $relayMasterLogFile;

    private ?bool $slaveIoRunning;
    private ?bool $slaveSqlRunning;

    private ?string $replicateDoDb;
    private ?string $replicateIgnoreDb;
    private ?string $replicateDoTable;
    private ?string $replicateIgnoreTable;
    private ?string $replicateWildDoTable;
    private ?string $replicateWildIgnoreTable;

    private ?int $lastErrno;
    private ?string $lastError;

    private ?int $skipCounter;
    private ?int $execMasterLogPos;
    private ?int $relayLogSpace;

    private ?string $untilCondition;
    private ?string $untilLogFile;
    private ?int $untilLogPos;

    private ?bool $masterSslAllowed;
    private ?string $masterSslCaFile;
    private ?string $masterSslCaPath;
    private ?string $masterSslCert;
    private ?string $masterSslCipher;
    private ?string $masterSslKey;

    private ?int $secondsBehindMaster;
    private ?bool $masterSslVerifyServerCert;

    private ?int $lastIoErrno;
    private ?string $lastIoError;
    private ?int $lastSqlErrno;
    private ?string $lastSqlError;

    private ?string $replicateIgnoreServerIds;

    private ?int $masterServerId;
    private ?string $masterUuid;
    private ?string $masterInfoFile;

    private ?int $sqlDelay;
    private ?int $sqlRemainingDelay;
    private ?string $slaveSqlRunningState;
    private ?int $masterRetryCount;

    private ?string $masterBind;
    private ?int $lastIoErrorTimestamp;
    private ?int $lastSqlErrorTimestamp;

    private ?string $masterSslCrl;
    private ?string $masterSslCrlpath;

    private ?string $retrievedGtidSet;
    private ?string $executedGtidSet;

    private ?int $autoPosition;
    private ?string $replicateRewriteDb;

    private ?string $channelName;
    private ?string $masterTlsVersion;

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(?int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getSlaveIoState(): ?string
    {
        return $this->slaveIoState;
    }

    public function setSlaveIoState(?string $slaveIoState): self
    {
        $this->slaveIoState = $slaveIoState;

        return $this;
    }

    public function getMasterHost(): ?string
    {
        return $this->masterHost;
    }

    public function setMasterHost(?string $masterHost): self
    {
        $this->masterHost = $masterHost;

        return $this;
    }

    public function getMasterUser(): ?string
    {
        return $this->masterUser;
    }

    public function setMasterUser(?string $masterUser): self
    {
        $this->masterUser = $masterUser;

        return $this;
    }

    public function getMasterPort(): ?int
    {
        return $this->masterPort;
    }

    public function setMasterPort(?int $masterPort): self
    {
        $this->masterPort = $masterPort;

        return $this;
    }

    public function getConnectRetry(): ?int
    {
        return $this->connectRetry;
    }

    public function setConnectRetry(?int $connectRetry): self
    {
        $this->connectRetry = $connectRetry;

        return $this;
    }

    public function getMasterLogFile(): ?string
    {
        return $this->masterLogFile;
    }

    public function setMasterLogFile(?string $masterLogFile): self
    {
        $this->masterLogFile = $masterLogFile;

        return $this;
    }

    public function getReadMasterLogPos(): ?int
    {
        return $this->readMasterLogPos;
    }

    public function setReadMasterLogPos(?int $readMasterLogPos): self
    {
        $this->readMasterLogPos = $readMasterLogPos;

        return $this;
    }

    public function getRelayLogFile(): ?string
    {
        return $this->relayLogFile;
    }

    public function setRelayLogFile(?string $relayLogFile): self
    {
        $this->relayLogFile = $relayLogFile;

        return $this;
    }

    public function getRelayLogPos(): ?int
    {
        return $this->relayLogPos;
    }

    public function setRelayLogPos(?int $relayLogPos): self
    {
        $this->relayLogPos = $relayLogPos;

        return $this;
    }

    public function getRelayMasterLogFile(): ?string
    {
        return $this->relayMasterLogFile;
    }

    public function setRelayMasterLogFile(?string $relayMasterLogFile): self
    {
        $this->relayMasterLogFile = $relayMasterLogFile;

        return $this;
    }

    public function getSlaveIoRunning(): ?bool
    {
        return $this->slaveIoRunning;
    }

    public function setSlaveIoRunning(?bool $slaveIoRunning): self
    {
        $this->slaveIoRunning = $slaveIoRunning;

        return $this;
    }

    public function getSlaveSqlRunning(): ?bool
    {
        return $this->slaveSqlRunning;
    }

    public function setSlaveSqlRunning(?bool $slaveSqlRunning): self
    {
        $this->slaveSqlRunning = $slaveSqlRunning;

        return $this;
    }

    public function getReplicateDoDb(): ?string
    {
        return $this->replicateDoDb;
    }

    public function setReplicateDoDb(?string $replicateDoDb): self
    {
        $this->replicateDoDb = $replicateDoDb;

        return $this;
    }

    public function getReplicateIgnoreDb(): ?string
    {
        return $this->replicateIgnoreDb;
    }

    public function setReplicateIgnoreDb(?string $replicateIgnoreDb): self
    {
        $this->replicateIgnoreDb = $replicateIgnoreDb;

        return $this;
    }

    public function getReplicateDoTable(): ?string
    {
        return $this->replicateDoTable;
    }

    public function setReplicateDoTable(?string $replicateDoTable): self
    {
        $this->replicateDoTable = $replicateDoTable;

        return $this;
    }

    public function getReplicateIgnoreTable(): ?string
    {
        return $this->replicateIgnoreTable;
    }

    public function setReplicateIgnoreTable(?string $replicateIgnoreTable): self
    {
        $this->replicateIgnoreTable = $replicateIgnoreTable;

        return $this;
    }

    public function getReplicateWildDoTable(): ?string
    {
        return $this->replicateWildDoTable;
    }

    public function setReplicateWildDoTable(?string $replicateWildDoTable): self
    {
        $this->replicateWildDoTable = $replicateWildDoTable;

        return $this;
    }

    public function getReplicateWildIgnoreTable(): ?string
    {
        return $this->replicateWildIgnoreTable;
    }

    public function setReplicateWildIgnoreTable(?string $replicateWildIgnoreTable): self
    {
        $this->replicateWildIgnoreTable = $replicateWildIgnoreTable;

        return $this;
    }

    public function getLastErrno(): ?int
    {
        return $this->lastErrno;
    }

    public function setLastErrno(?int $lastErrno): self
    {
        $this->lastErrno = $lastErrno;

        return $this;
    }

    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    public function setLastError(?string $lastError): self
    {
        $this->lastError = $lastError;

        return $this;
    }

    public function getSkipCounter(): ?int
    {
        return $this->skipCounter;
    }

    public function setSkipCounter(?int $skipCounter): self
    {
        $this->skipCounter = $skipCounter;

        return $this;
    }

    public function getExecMasterLogPos(): ?int
    {
        return $this->execMasterLogPos;
    }

    public function setExecMasterLogPos(?int $execMasterLogPos): self
    {
        $this->execMasterLogPos = $execMasterLogPos;

        return $this;
    }

    public function getRelayLogSpace(): ?int
    {
        return $this->relayLogSpace;
    }

    public function setRelayLogSpace(?int $relayLogSpace): self
    {
        $this->relayLogSpace = $relayLogSpace;

        return $this;
    }

    public function getUntilCondition(): ?string
    {
        return $this->untilCondition;
    }

    public function setUntilCondition(?string $untilCondition): self
    {
        $this->untilCondition = $untilCondition;

        return $this;
    }

    public function getUntilLogFile(): ?string
    {
        return $this->untilLogFile;
    }

    public function setUntilLogFile(?string $untilLogFile): self
    {
        $this->untilLogFile = $untilLogFile;

        return $this;
    }

    public function getUntilLogPos(): ?int
    {
        return $this->untilLogPos;
    }

    public function setUntilLogPos(?int $untilLogPos): self
    {
        $this->untilLogPos = $untilLogPos;

        return $this;
    }

    public function getMasterSslAllowed(): ?bool
    {
        return $this->masterSslAllowed;
    }

    public function setMasterSslAllowed(?bool $masterSslAllowed): self
    {
        $this->masterSslAllowed = $masterSslAllowed;

        return $this;
    }

    public function getMasterSslCaFile(): ?string
    {
        return $this->masterSslCaFile;
    }

    public function setMasterSslCaFile(?string $masterSslCaFile): self
    {
        $this->masterSslCaFile = $masterSslCaFile;

        return $this;
    }

    public function getMasterSslCaPath(): ?string
    {
        return $this->masterSslCaPath;
    }

    public function setMasterSslCaPath(?string $masterSslCaPath): self
    {
        $this->masterSslCaPath = $masterSslCaPath;

        return $this;
    }

    public function getMasterSslCert(): ?string
    {
        return $this->masterSslCert;
    }

    public function setMasterSslCert(?string $masterSslCert): self
    {
        $this->masterSslCert = $masterSslCert;

        return $this;
    }

    public function getMasterSslCipher(): ?string
    {
        return $this->masterSslCipher;
    }

    public function setMasterSslCipher(?string $masterSslCipher): self
    {
        $this->masterSslCipher = $masterSslCipher;

        return $this;
    }

    public function getMasterSslKey(): ?string
    {
        return $this->masterSslKey;
    }

    public function setMasterSslKey(?string $masterSslKey): self
    {
        $this->masterSslKey = $masterSslKey;

        return $this;
    }

    public function getSecondsBehindMaster(): ?int
    {
        return $this->secondsBehindMaster;
    }

    public function setSecondsBehindMaster(?int $secondsBehindMaster): self
    {
        $this->secondsBehindMaster = $secondsBehindMaster;

        return $this;
    }

    public function getMasterSslVerifyServerCert(): ?bool
    {
        return $this->masterSslVerifyServerCert;
    }

    public function setMasterSslVerifyServerCert(?bool $masterSslVerifyServerCert): self
    {
        $this->masterSslVerifyServerCert = $masterSslVerifyServerCert;

        return $this;
    }

    public function getLastIoErrno(): ?int
    {
        return $this->lastIoErrno;
    }

    public function setLastIoErrno(?int $lastIoErrno): self
    {
        $this->lastIoErrno = $lastIoErrno;

        return $this;
    }

    public function getLastIoError(): ?string
    {
        return $this->lastIoError;
    }

    public function setLastIoError(?string $lastIoError): self
    {
        $this->lastIoError = $lastIoError;

        return $this;
    }

    public function getLastSqlErrno(): ?int
    {
        return $this->lastSqlErrno;
    }

    public function setLastSqlErrno(?int $lastSqlErrno): self
    {
        $this->lastSqlErrno = $lastSqlErrno;

        return $this;
    }

    public function getLastSqlError(): ?string
    {
        return $this->lastSqlError;
    }

    public function setLastSqlError(?string $lastSqlError): self
    {
        $this->lastSqlError = $lastSqlError;

        return $this;
    }

    public function getReplicateIgnoreServerIds(): ?string
    {
        return $this->replicateIgnoreServerIds;
    }

    public function setReplicateIgnoreServerIds(?string $replicateIgnoreServerIds): self
    {
        $this->replicateIgnoreServerIds = $replicateIgnoreServerIds;

        return $this;
    }

    public function getMasterServerId(): ?int
    {
        return $this->masterServerId;
    }

    public function setMasterServerId(?int $masterServerId): self
    {
        $this->masterServerId = $masterServerId;

        return $this;
    }

    public function getMasterUuid(): ?string
    {
        return $this->masterUuid;
    }

    public function setMasterUuid(?string $masterUuid): self
    {
        $this->masterUuid = $masterUuid;

        return $this;
    }

    public function getMasterInfoFile(): ?string
    {
        return $this->masterInfoFile;
    }

    public function setMasterInfoFile(?string $masterInfoFile): self
    {
        $this->masterInfoFile = $masterInfoFile;

        return $this;
    }

    public function getSqlDelay(): ?int
    {
        return $this->sqlDelay;
    }

    public function setSqlDelay(?int $sqlDelay): self
    {
        $this->sqlDelay = $sqlDelay;

        return $this;
    }

    public function getSqlRemainingDelay(): ?int
    {
        return $this->sqlRemainingDelay;
    }

    public function setSqlRemainingDelay(?int $sqlRemainingDelay): self
    {
        $this->sqlRemainingDelay = $sqlRemainingDelay;

        return $this;
    }

    public function getSlaveSqlRunningState(): ?string
    {
        return $this->slaveSqlRunningState;
    }

    public function setSlaveSqlRunningState(?string $slaveSqlRunningState): self
    {
        $this->slaveSqlRunningState = $slaveSqlRunningState;

        return $this;
    }

    public function getMasterRetryCount(): ?int
    {
        return $this->masterRetryCount;
    }

    public function setMasterRetryCount(?int $masterRetryCount): self
    {
        $this->masterRetryCount = $masterRetryCount;

        return $this;
    }

    public function getMasterBind(): ?string
    {
        return $this->masterBind;
    }

    public function setMasterBind(?string $masterBind): self
    {
        $this->masterBind = $masterBind;

        return $this;
    }

    public function getLastIoErrorTimestamp(): ?int
    {
        return $this->lastIoErrorTimestamp;
    }

    public function setLastIoErrorTimestamp(?int $lastIoErrorTimestamp): self
    {
        $this->lastIoErrorTimestamp = $lastIoErrorTimestamp;

        return $this;
    }

    public function getLastSqlErrorTimestamp(): ?int
    {
        return $this->lastSqlErrorTimestamp;
    }

    public function setLastSqlErrorTimestamp(?int $lastSqlErrorTimestamp): self
    {
        $this->lastSqlErrorTimestamp = $lastSqlErrorTimestamp;

        return $this;
    }

    public function getMasterSslCrl(): ?string
    {
        return $this->masterSslCrl;
    }

    public function setMasterSslCrl(?string $masterSslCrl): self
    {
        $this->masterSslCrl = $masterSslCrl;

        return $this;
    }

    public function getMasterSslCrlpath(): ?string
    {
        return $this->masterSslCrlpath;
    }

    public function setMasterSslCrlpath(?string $masterSslCrlpath): self
    {
        $this->masterSslCrlpath = $masterSslCrlpath;

        return $this;
    }

    public function getRetrievedGtidSet(): ?string
    {
        return $this->retrievedGtidSet;
    }

    public function setRetrievedGtidSet(?string $retrievedGtidSet): self
    {
        $this->retrievedGtidSet = $retrievedGtidSet;

        return $this;
    }

    public function getExecutedGtidSet(): ?string
    {
        return $this->executedGtidSet;
    }

    public function setExecutedGtidSet(?string $executedGtidSet): self
    {
        $this->executedGtidSet = $executedGtidSet;

        return $this;
    }

    public function getAutoPosition(): ?int
    {
        return $this->autoPosition;
    }

    public function setAutoPosition(?int $autoPosition): self
    {
        $this->autoPosition = $autoPosition;

        return $this;
    }

    public function getReplicateRewriteDb(): ?string
    {
        return $this->replicateRewriteDb;
    }

    public function setReplicateRewriteDb(?string $replicateRewriteDb): self
    {
        $this->replicateRewriteDb = $replicateRewriteDb;

        return $this;
    }

    public function getChannelName(): ?string
    {
        return $this->channelName;
    }

    public function setChannelName(?string $channelName): self
    {
        $this->channelName = $channelName;

        return $this;
    }

    public function getMasterTlsVersion(): ?string
    {
        return $this->masterTlsVersion;
    }

    public function setMasterTlsVersion(?string $masterTlsVersion): self
    {
        $this->masterTlsVersion = $masterTlsVersion;

        return $this;
    }

    public function setFromRow(array $row)
    {
        $fields = [
            'Slave_IO_State' => 'setSlaveIoState',
            'Master_Host' => 'setMasterHost',
            'Master_User' => 'setMasterUser',
            'Master_Port' => 'setMasterPort',
            'Connect_Retry' => 'setConnectRetry',
            'Master_Log_File' => 'setMasterLogFile',
            'Read_Master_Log_Pos' => 'setReadMasterLogPos',
            'Relay_Log_File' => 'setRelayLogFile',
            'Relay_Log_Pos' => 'setRelayLogPos',
            'Relay_Master_Log_Pos' => 'setRelayMasterLogPos',
            'Slave_IO_Running' => 'setSlaveIoRunning',
            'Slave_SQL_Running' => 'setSlaveSqlRunning',
            'Replicate_Do_DB' => 'setReplicateDoDb',
            'Replicate_Ignore_DB' => 'setReplicateIgnoreDb',
            'Replicate_Do_Table' => 'setReplicateDoTable',
            'Replicate_Ignore_Table' => 'setReplicateIgnoreTable',
            'Replicate_Wild_Do_Table' => 'setReplicateWildDoTable',
            'Replicate_Wild_Ignore_Table' => 'setReplicateWildIgnoreTable',
            'Last_Errno' => 'setLastErrno',
            'Last_Error' => 'setLastError',
            'Skip_Counter' => 'setSkipCounter',
            'Exec_Master_Log_Pos' => 'setExecMasterLogPos',
            'Relay_Log_Space' => 'setRelayLogSpace',
            'Until_Condition' => 'setUntilCondition',
            'Until_Log_File' => 'setUntilLogFile',
            'Until_Log_Pos' => 'setUntilLogPos',
            'Master_SSL_Allowed' => 'setMasterSslAllowed',
            'Master_SSL_CA_File' => 'setMasterSslCaFile',
            'Master_SSL_CA_Path' => 'setMasterSslCaPath',
            'Master_SSL_Cert' => 'setMasterSslCert',
            'Master_SSL_Cipher' => 'setMasterSslCipher',
            'Master_SSL_Key' => 'setMasterSslKey',
            'Seconds_Behind_Master' => 'setSecondsBehindMaster',
            'Master_SSL_Verify_Server_Cert' => 'setMasterSslVerifyServerCert',
            'Last_IO_Errno' => 'setLastIoErrno',
            'Last_IO_Error' => 'setLastIoError',
            'Last_SQL_Errno' => 'setLastSqlErrno',
            'Last_SQL_Error' => 'setLastSqlError',
            'Master_Server_Id' => 'setMasterServerId',
            'Master_UUID' => 'setMasterUuid',
            'Master_Info_File' => 'setMasterInfoFile',
            'SQL_Delay' => 'setSqlDelay',
            'SQL_Remaining_Delay' => 'setSqlRemainingDelay',
            'Slave_SQL_Running_State' => 'setSlaveSqlRunningState',
            'Master_Retry_Count' => 'setMasterRetryCount',
            'Master_Bind' => 'setMasterBind',
            'Last_IO_Error_Timestamp' => 'setLastIoErrorTimestamp',
            'Last_SQL_Error_Timestamp' => 'setLastSqlErrorTimestamp',
            'Master_SSL_Crl' => 'setMasterSslCrl',
            'Master_SSL_Crlpath' => 'setMasterSslCrlpath',
            'Retrieved_Gtid_Set' => 'setRetrievedGtidSet',
            'Executed_Gtid_Set' => 'setExecutedGtidSet',
            'Auto_Position' => 'setAutoPosition',
            'Replicate_Rewrite_DB' => 'setReplicateRewriteDb',
            'Channel_Name' => 'setChannelName',
            'Master_TLS_Version' => 'setMasterTlsVersion',
        ];

        $transforms = [
            'Slave_IO_Running' => 'YesNo',
            'Slave_SQL_Running' => 'YesNo',
            'Master_SSL_Allowed' => 'YesNo',
            'Master_SSL_Verify_Server_Cert' => 'YesNo',
            'Last_IO_Error_Timestamp' => 'int_null',
            'Last_SQL_Error_Timestamp' => 'int_null',
        ];
        foreach ($row as $key => $rawValue) {
            if (isset($fields[$key])) {
                $setter = $fields[$key];

                if (isset($transforms[$key])) {
                    switch ($transforms[$key]) {
                        case 'YesNo':
                            switch (strtolower($rawValue)) {
                                case 'yes':
                                    $value = true;
                                    break;
                                case 'no':
                                    $value = false;
                                    break;
                                default:
                                    $value = null;
                            }
                            break;
                        case 'int_null':
                            $value = ('' === $rawValue ? null : $rawValue + 0);
                            break;
                        default:
                            throw new \Exception(sprintf('unsupported transformer %s', $transforms[$key]));
                    }
                } else {
                    $value = $rawValue;
                }
                $this->$setter($value);
            }
        }
    }

    public static function createFromRow(array $row): self
    {
        $status = new self();
        $status->setFromRow($row);

        return $status;
    }
}
