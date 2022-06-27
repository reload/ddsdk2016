<?php

namespace Drupal\dds_track;

/**
 * Provides an interface for dds_track.
 */
interface TrackInterface {

  /**
   * The path to the frontpage of the track-universe.
   */
  const TRACK_UNIVERSE_FRONTPAGE = '/track';

  /**
   * The ID of the parent track term.
   *
   * Since the name of the "Track" term can change, this is a helper function
   * that returns the id of the term associated with the "Track" term.
   */
  const TRACK_TERM_ID = 834;

}
