<?php
 /**
 * Part of the mara-platform package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    mara-platform
 * @version    1.0.0
 * @author     Nematix LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 1997 - 2014, Nematix Corporation
 * @link       http://nematix.com
 */
namespace Atlantis\Api\Exception;

/**
 * Interface for exceptions that can provide additional API Problem details
 */
interface ProblemExceptionInterface
{
    public function getAdditionalDetails();
    public function getDescribedBy();
    public function getTitle();
}