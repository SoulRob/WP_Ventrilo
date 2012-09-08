<?php
include_once "VentriloService.php";
include_once "core/ventrilostatus.php";

class VentriloServiceDirect extends VentriloService {
	function get_XML($host, $port, $pass) {
			
		$stat = new CVentriloStatus;
		$stat->m_cmdprog	= __DIR__."/core/ventrilo_status";
		$stat->m_cmdcode	= "2";
		$stat->m_cmdhost    = $host;
		$stat->m_cmdport	= $port;
		$stat->m_cmdpass	= $pass;

		$rc = $stat->Request();
		
		// Throw Exception if something's wrong
		if ( $rc ) {
			throw new ServiceException($stat->m_error, $rc);
		}
		
		$xml = "<ventrilo>\n";
		$xml .= "<server>\n";
		$xml .= "<host>".$stat->m_cmdhost."</host>\n";
		$xml .= "<port>".$stat->m_cmdport."</port>\n";
		$xml .= "<name>".$this->cdata($stat->m_name)."</name>\n";
		$xml .= "<link>ventrilo://".$stat->m_cmdhost.":".$stat->m_cmdport."/servername=".urlencode($stat->m_name)."</link>\n";
		$xml .= "<phonetic>".$stat->m_phonetic."</phonetic>\n";
		if(strlen($stat->m_comment)>0) $xml .= "<comment>".$this->cdata($stat->m_comment)."</comment>\n";
		$xml .= "<auth>".$stat->m_auth."</auth>\n";
		$xml .= "<maxclients>".$stat->m_maxclients."</maxclients>\n";
		$xml .= "<voicecodec>".$stat->m_voicecodec_desc."</voicecodec>\n";
		$xml .= "<voiceformat>".$stat->m_voiceformat_desc."</voiceformat>\n";
		$xml .= "<uptime>".$stat->m_uptime."</uptime>\n";
		$xml .= "<platform>".$stat->m_platform."</platform>\n";
		$xml .= "<version>".$stat->m_version."</version>\n";
		$xml .= "<channelcount>".$stat->m_channelcount."</channelcount>\n";
		$xml .= "<clientcount>".$stat->m_clientcount."</clientcount>\n";
		$xml .= "</server>\n";

		if ( !$rc ) {
			$xml .= $this->getChannel($stat, 0);
		}

		$xml .= "</ventrilo>";
		
		return utf8_encode($xml);
	}


	function getChannel($stat, $cid, $children=true) {
		$isLobby = $cid == 0;

		$chan = $stat->ChannelFind( $cid );
		$xml = "<channel>\n";
		$xml .= "<id>".($isLobby ? "0" : $chan->m_cid)."</id>\n";
		$xml .= "<name>".($isLobby ? "Lobby" : $this->cdata($chan->m_name))."</name>\n";
		if(!$isLobby) {
			$xml .= "<pid>".$chan->m_pid."</pid>\n";
			$xml .= "<pass>".$chan->m_prot."</pass>\n";	
			if(strlen($chan->m_comm)>0) $xml .= "<comment>".$this->cdata($chan->m_comm)."></comment>\n";
		}
		
		$xml .= $this->getClients($stat, $isLobby ? "0" : $chan->m_cid);

		for ( $i = 0; $i < count( $stat->m_channellist ); $i++ ) {
			if ( $stat->m_channellist[ $i ]->m_pid == $cid ) {
				$subcid = $stat->m_channellist[ $i ]->m_cid;
					
				$xml .= $this->getChannel($stat, $subcid);
			}
		}

		$xml .= "</channel>\n";
		
		return $xml;
	}

	function getClients(&$stat, $inChannel=NULL) {
		$count = 0;
		$xml = "<clients>\n";
		for ( $i = 0; $i < count( $stat->m_clientlist ); $i++ )	{

			$client = $stat->m_clientlist[ $i ];

			if($inChannel != NULL && $client->m_cid != $inChannel)
				continue;

			$xml .= "<client>\n";
			$xml .= "<name>".$this->cdata($client->m_name)."</name>\n";
			$xml .= "<uid>".$client->m_uid."</uid>\n";
			$xml .= "<cid>".$client->m_cid."</cid>\n";
			
			if ( $client->m_admin ) {
				$xml .= "<admin/>";
			}

			if ( $client->m_phan ) {
				$xml .= "<phantom/>";
			}

			$xml .= "<sec>".$client->m_sec."</sec>\n";
			$xml .= "<ping>".$client->m_ping."</ping>\n";
			
			if(strlen($client->m_comm)>0) $xml .= "<comment>".$this->cdata($client->m_comm)."</comment>\n";
			
			$count++;
			$xml .= "</client>\n";
		}
		
		$xml .= "</clients>\n";
	
		if($count == 0) {
			return "<clients/>";
		} else {
			return  $xml;
		}
	}
}
?>