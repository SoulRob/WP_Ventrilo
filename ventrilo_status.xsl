<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="2.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:f="http://robweeks.net/xslt/functions">

	<xsl:variable name="image_server" select="'http://www.ventrilo.com/venticon_server.png'" />
 	<xsl:variable name="image_channel_parent" select="'http://www.ventrilo.com/venticon_chanopen.png'" />
 	<xsl:variable name="image_channel" select="'http://www.ventrilo.com/venticon_chan.png'" />

	<xsl:template match="ventrilo">
		<!-- Server's name with a link -->
		<img>
			<xsl:attribute name="src"><xsl:value-of select="$image_server"/></xsl:attribute>
		</img>
		<a>
			<xsl:attribute name="href"><xsl:value-of select="server/link"/></xsl:attribute>
			<font style="text-align:center; font-weight:bold; font-variant:small-caps; text-decoration:underline; color: black; ">
				<xsl:value-of select="server/name"/>
			</font>
		</a>

		<br/>
	
		<xsl:apply-templates select="channel"/>
	</xsl:template>

	<!-- Renders a channel and all sub elements -->
	<xsl:template match="channel">

			<img>
				<xsl:attribute name="src">
					<xsl:choose>
						<xsl:when test="channel"><xsl:value-of select="$image_channel_parent"/></xsl:when>
						<xsl:otherwise><xsl:value-of select="$image_channel"/></xsl:otherwise>
					</xsl:choose>
				</xsl:attribute>
			</img>
			<xsl:value-of select="name"/>

			<xsl:apply-templates select="clients/client" />
		<ul>
			<xsl:apply-templates select="channel" />
		</ul>
	</xsl:template>

	<!-- Rendered a client that's inside a channel -->
	<xsl:template match="client">
		<ul>
			<b><xsl:value-of select="name"/></b>
		</ul>
	</xsl:template>
</xsl:stylesheet>
