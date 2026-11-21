-- =====================================================================================
-- Trigger: trg_tender_enquiry_item_after_update_cascade_status
-- Purpose: When a tender enquiry item (tender_enquiry_item_info) status is set to 'Deleted' or 'Delete',
--          automatically cascade the status, updated_by, and updated_date to:
--            1. tender_quotation_item_info
--            2. vendor_rate_enquiry_item_info
-- =====================================================================================

DELIMITER $$

DROP TRIGGER IF EXISTS `trg_tender_enquiry_item_after_update_cascade_status`$$

CREATE TRIGGER `trg_tender_enquiry_item_after_update_cascade_status`
AFTER UPDATE ON `tender_enquiry_item_info`
FOR EACH ROW
BEGIN
    -- Check if status transitioned to a deleted state ('Deleted' or 'Delete')
    IF (NEW.status IN ('Deleted', 'Delete') AND (OLD.status NOT IN ('Deleted', 'Delete') OR OLD.status IS NULL)) THEN
        
        -- 1. Cascade to tender_quotation_item_info
        UPDATE tender_quotation_item_info
        SET status = NEW.status,
            updated_by = COALESCE(NEW.updated_by, updated_by),
            updated_date = COALESCE(NEW.updated_date, NOW())
        WHERE tender_enquiry_item_id = NEW.tender_enquiry_item_id
          AND (status NOT IN ('Deleted', 'Delete') OR status IS NULL);

        -- 2. Cascade to vendor_rate_enquiry_item_info
        UPDATE vendor_rate_enquiry_item_info
        SET status = NEW.status,
            updated_by = COALESCE(NEW.updated_by, updated_by),
            updated_date = COALESCE(NEW.updated_date, NOW())
        WHERE tender_enquiry_item_id = NEW.tender_enquiry_item_id
          AND (status NOT IN ('Deleted', 'Delete') OR status IS NULL);

    END IF;
END$$

DELIMITER ;

-- =====================================================================================
-- Historical Data Synchronization (One-Time Run)
-- =====================================================================================

-- 1. Sync tender_quotation_item_info
UPDATE tender_quotation_item_info q_item
JOIN tender_enquiry_item_info te_item ON q_item.tender_enquiry_item_id = te_item.tender_enquiry_item_id
SET q_item.status = te_item.status,
    q_item.updated_date = NOW()
WHERE te_item.status IN ('Delete', 'Deleted')
  AND q_item.status = 'Active';

-- 2. Sync vendor_rate_enquiry_item_info
UPDATE vendor_rate_enquiry_item_info v_item
JOIN tender_enquiry_item_info te_item ON v_item.tender_enquiry_item_id = te_item.tender_enquiry_item_id
SET v_item.status = te_item.status,
    v_item.updated_date = NOW()
WHERE te_item.status IN ('Delete', 'Deleted')
  AND v_item.status = 'Active';

