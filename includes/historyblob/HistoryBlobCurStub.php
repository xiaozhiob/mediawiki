<?php
/**
 * Efficient concatenated text storage.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 */

/**
 * To speed up conversion from 1.4 to 1.5 schema, text rows can refer to the
 * leftover cur table as the backend. This avoids expensively copying hundreds
 * of megabytes of data during the conversion downtime.
 *
 * Serialized HistoryBlobCurStub objects will be inserted into the text table
 * on conversion if $wgLegacySchemaConversion is set to true.
 */
class HistoryBlobCurStub {
	/** @var int */
	public $mCurId;

	/**
	 * @param int $curid The cur_id pointed to
	 */
	function __construct( $curid = 0 ) {
		$this->mCurId = $curid;
	}

	/**
	 * Sets the location (cur_id) of the main object to which this object
	 * points
	 *
	 * @param int $id
	 */
	function setLocation( $id ) {
		$this->mCurId = $id;
	}

	/**
	 * @return string|bool
	 */
	function getText() {
		$dbr = wfGetDB( DB_REPLICA );
		$row = $dbr->selectRow( 'cur', [ 'cur_text' ], [ 'cur_id' => $this->mCurId ] );
		if ( !$row ) {
			return false;
		}
		return $row->cur_text;
	}
}

// phpcs:ignore Generic.CodeAnalysis.UnconditionalIfStatement.Found
if ( false ) {
	// Blobs generated by MediaWiki < 1.5 on PHP 4 were serialized with the
	// class name coerced to lowercase. We can improve efficiency by adding
	// autoload entries for the lowercase variants of these classes (T166759).
	// The code below is never executed, but it is picked up by the AutoloadGenerator
	// parser, which scans for class_alias() calls.
	// @phan-suppress-next-line PhanRedefineClassAlias
	class_alias( HistoryBlobCurStub::class, 'historyblobcurstub' );
}
